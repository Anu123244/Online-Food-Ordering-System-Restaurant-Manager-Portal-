<?php
require_once __DIR__ . '/../core/Database.php';

class RestaurantManagerModel {
    private mysqli $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function createManagerRegistration(array $data): array {
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $managerName = trim($data['manager_name'] ?? '');
        $restaurantName = trim($data['restaurant_name'] ?? '');
        $city = trim($data['city'] ?? '');

        if ($email === '' || $password === '' || $managerName === '' || $restaurantName === '' || $city === '') {
            return ['success' => false, 'message' => 'Manager name, email, password, restaurant name, and city are required.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Please enter a valid email address.'];
        }

        $check = $this->db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $check->bind_param('s', $email);
        $check->execute();
        if ($check->get_result()->fetch_assoc()) {
            return ['success' => false, 'message' => 'This email is already registered.'];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $phone = trim($data['phone'] ?? '');
        $description = trim($data['description'] ?? '');
        $cuisine = trim($data['cuisine_type'] ?? '');
        $address = trim($data['address'] ?? '');
        $openingHours = trim($data['opening_hours'] ?? '');
        $radius = (float)($data['delivery_radius_km'] ?? 5);

        try {
            $this->db->begin_transaction();
            $role = 'manager';
            $isActive = 1;
            $userStmt = $this->db->prepare("INSERT INTO users (name, email, password_hash, phone, role, is_active) VALUES (?, ?, ?, ?, ?, ?)");
            $userStmt->bind_param('sssssi', $managerName, $email, $hash, $phone, $role, $isActive);
            $userStmt->execute();
            $managerId = $this->db->insert_id;

            $isOpen = 0;
            $isApproved = 0;
            $restaurantStmt = $this->db->prepare("\n                INSERT INTO restaurants (manager_id, name, description, cuisine_type, address, city, opening_hours, delivery_radius_km, is_open, is_approved)\n                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\n            ");
            $restaurantStmt->bind_param('issssssdii', $managerId, $restaurantName, $description, $cuisine, $address, $city, $openingHours, $radius, $isOpen, $isApproved);
            $restaurantStmt->execute();
            $this->db->commit();
            return ['success' => true, 'message' => 'Restaurant registration submitted. Please wait for admin approval before logging in.'];
        } catch (Throwable $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }

    public function restaurantByManager(int $managerId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM restaurants WHERE manager_id = ? LIMIT 1");
        $stmt->bind_param('i', $managerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function dashboardStats(int $restaurantId): array {
        $stmt = $this->db->prepare("\n            SELECT\n                COUNT(*) AS total_orders,\n                SUM(CASE WHEN status IN ('pending','accepted','preparing','ready') THEN 1 ELSE 0 END) AS active_orders,\n                COALESCE(SUM(CASE WHEN status <> 'cancelled' THEN total_amount ELSE 0 END), 0) AS total_revenue,\n                COALESCE(AVG(CASE WHEN status <> 'cancelled' THEN total_amount ELSE NULL END), 0) AS avg_order_value\n            FROM orders WHERE restaurant_id = ?\n        ");
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function incomingOrders(int $restaurantId): array {
        $stmt = $this->db->prepare("\n            SELECT o.*, u.name AS customer_name,\n                   GROUP_CONCAT(CONCAT(mi.name, ' x', oi.quantity) ORDER BY mi.name SEPARATOR ', ') AS item_summary\n            FROM orders o\n            JOIN users u ON u.id = o.customer_id\n            LEFT JOIN order_items oi ON oi.order_id = o.id\n            LEFT JOIN menu_items mi ON mi.id = oi.menu_item_id\n            WHERE o.restaurant_id = ? AND o.status IN ('pending','accepted','preparing','ready')\n            GROUP BY o.id\n            ORDER BY FIELD(o.status, 'pending','accepted','preparing','ready'), o.created_at DESC\n        ");
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function groupedActiveOrders(int $restaurantId): array {
        $orders = $this->incomingOrders($restaurantId);
        $groups = ['pending' => [], 'accepted' => [], 'preparing' => [], 'ready' => []];
        foreach ($orders as $order) {
            $groups[$order['status']][] = $order;
        }
        return $groups;
    }

    public function orderHistory(int $restaurantId): array {
        $stmt = $this->db->prepare("\n            SELECT o.*, u.name AS customer_name,\n                   GROUP_CONCAT(CONCAT(mi.name, ' x', oi.quantity, ' @ ৳', FORMAT(oi.unit_price, 2)) ORDER BY mi.name SEPARATOR '; ') AS item_summary\n            FROM orders o\n            JOIN users u ON u.id = o.customer_id\n            LEFT JOIN order_items oi ON oi.order_id = o.id\n            LEFT JOIN menu_items mi ON mi.id = oi.menu_item_id\n            WHERE o.restaurant_id = ?\n            GROUP BY o.id\n            ORDER BY o.created_at DESC\n        ");
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function orderItems(int $orderId): array {
        $stmt = $this->db->prepare("\n            SELECT mi.name, oi.quantity, oi.unit_price\n            FROM order_items oi\n            JOIN menu_items mi ON mi.id = oi.menu_item_id\n            WHERE oi.order_id = ?\n        ");
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function menuItems(int $restaurantId): array {
        $stmt = $this->db->prepare("\n            SELECT mi.*, mc.name AS category_name\n            FROM menu_items mi\n            LEFT JOIN menu_categories mc ON mc.id = mi.category_id\n            WHERE mi.restaurant_id = ?\n            ORDER BY mc.display_order, mi.name\n        ");
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function menuItemById(int $itemId, int $restaurantId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM menu_items WHERE id = ? AND restaurant_id = ? LIMIT 1");
        $stmt->bind_param('ii', $itemId, $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function categories(int $restaurantId): array {
        $stmt = $this->db->prepare("SELECT * FROM menu_categories WHERE restaurant_id = ? ORDER BY display_order, name");
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function saveCategory(int $restaurantId, array $data): bool {
        $id = (int)($data['id'] ?? 0);
        $name = trim($data['name'] ?? '');
        $displayOrder = (int)($data['display_order'] ?? 0);
        if ($name === '') return false;

        if ($id > 0) {
            $stmt = $this->db->prepare("UPDATE menu_categories SET name = ?, display_order = ? WHERE id = ? AND restaurant_id = ?");
            $stmt->bind_param('siii', $name, $displayOrder, $id, $restaurantId);
            return $stmt->execute();
        }

        $stmt = $this->db->prepare("INSERT INTO menu_categories (restaurant_id, name, display_order) VALUES (?, ?, ?)");
        $stmt->bind_param('isi', $restaurantId, $name, $displayOrder);
        return $stmt->execute();
    }

    public function deleteCategory(int $restaurantId, int $categoryId): bool {
        $stmt = $this->db->prepare("DELETE FROM menu_categories WHERE id = ? AND restaurant_id = ?");
        $stmt->bind_param('ii', $categoryId, $restaurantId);
        return $stmt->execute();
    }

    public function saveMenuItem(int $restaurantId, array $data): bool {
        $id = (int)($data['id'] ?? 0);
        $categoryId = (int)($data['category_id'] ?? 0);
        $categoryId = $categoryId > 0 ? $categoryId : null;
        $name = trim($data['name'] ?? '');
        $description = trim($data['description'] ?? '');
        $price = (float)($data['price'] ?? 0);
        $imagePath = $data['image_path'] ?? null;
        $isAvailable = isset($data['is_available']) ? 1 : 0;

        if ($name === '' || $price <= 0) return false;

        if ($id > 0) {
            $stmt = $this->db->prepare("\n                UPDATE menu_items\n                SET category_id = ?, name = ?, description = ?, price = ?, image_path = ?, is_available = ?\n                WHERE id = ? AND restaurant_id = ?\n            ");
            $stmt->bind_param('issdsiii', $categoryId, $name, $description, $price, $imagePath, $isAvailable, $id, $restaurantId);
            return $stmt->execute();
        }

        $stmt = $this->db->prepare("\n            INSERT INTO menu_items (restaurant_id, category_id, name, description, price, image_path, is_available)\n            VALUES (?, ?, ?, ?, ?, ?, ?)\n        ");
        $stmt->bind_param('iissdsi', $restaurantId, $categoryId, $name, $description, $price, $imagePath, $isAvailable);
        return $stmt->execute();
    }

    public function deleteMenuItem(int $restaurantId, int $itemId): bool {
        $stmt = $this->db->prepare("DELETE FROM menu_items WHERE id = ? AND restaurant_id = ?");
        $stmt->bind_param('ii', $itemId, $restaurantId);
        return $stmt->execute();
    }

    public function reviews(int $restaurantId): array {
        $stmt = $this->db->prepare("\n            SELECT r.*, u.name AS customer_name\n            FROM reviews r\n            JOIN users u ON u.id = r.customer_id\n            WHERE r.restaurant_id = ?\n            ORDER BY r.created_at DESC\n        ");
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateReviewReply(int $restaurantId, int $reviewId, string $reply): bool {
        $stmt = $this->db->prepare("UPDATE reviews SET manager_reply = ? WHERE id = ? AND restaurant_id = ?");
        $stmt->bind_param('sii', $reply, $reviewId, $restaurantId);
        return $stmt->execute();
    }

    public function updateOrderStatus(int $orderId, int $restaurantId, string $status): bool {
        $allowed = ['accepted','preparing','ready','cancelled'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }
        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ? AND restaurant_id = ?");
        $stmt->bind_param('sii', $status, $orderId, $restaurantId);
        return $stmt->execute();
    }

    public function toggleMenuAvailability(int $itemId, int $restaurantId): bool {
        $stmt = $this->db->prepare("UPDATE menu_items SET is_available = IF(is_available = 1, 0, 1) WHERE id = ? AND restaurant_id = ?");
        $stmt->bind_param('ii', $itemId, $restaurantId);
        return $stmt->execute();
    }

    public function updateProfile(int $restaurantId, array $data): bool {
        $stmt = $this->db->prepare("\n            UPDATE restaurants\n            SET name = ?, description = ?, cuisine_type = ?, address = ?, city = ?, logo_path = ?, opening_hours = ?, delivery_radius_km = ?, is_open = ?\n            WHERE id = ?\n        ");
        $stmt->bind_param(
            'sssssssdii',
            $data['name'],
            $data['description'],
            $data['cuisine_type'],
            $data['address'],
            $data['city'],
            $data['logo_path'],
            $data['opening_hours'],
            $data['delivery_radius_km'],
            $data['is_open'],
            $restaurantId
        );
        return $stmt->execute();
    }

    public function discounts(int $restaurantId): array {
        $stmt = $this->db->prepare("\n            SELECT d.*, mi.name AS item_name,\n                   COUNT(DISTINCT o.id) AS orders_used,\n                   COALESCE(SUM(oi.quantity), 0) AS items_sold_during_campaign\n            FROM discounts d\n            JOIN menu_items mi ON mi.id = d.menu_item_id\n            LEFT JOIN order_items oi ON oi.menu_item_id = d.menu_item_id\n            LEFT JOIN orders o ON o.id = oi.order_id\n                AND o.restaurant_id = d.restaurant_id\n                AND o.status <> 'cancelled'\n                AND o.created_at BETWEEN d.valid_from AND d.valid_until\n            WHERE d.restaurant_id = ?\n            GROUP BY d.id\n            ORDER BY d.valid_from DESC\n        ");
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function saveDiscount(int $restaurantId, array $data): bool {
        $id = (int)($data['id'] ?? 0);
        $menuItemId = (int)($data['menu_item_id'] ?? 0);
        $discountPct = (float)($data['discount_pct'] ?? 0);
        $validFrom = str_replace('T', ' ', trim($data['valid_from'] ?? ''));
        $validUntil = str_replace('T', ' ', trim($data['valid_until'] ?? ''));
        $isActive = isset($data['is_active']) ? 1 : 0;

        if ($menuItemId <= 0 || $discountPct <= 0 || $discountPct > 100 || $validFrom === '' || $validUntil === '') {
            return false;
        }

        if ($id > 0) {
            $stmt = $this->db->prepare("\n                UPDATE discounts\n                SET menu_item_id = ?, discount_pct = ?, valid_from = ?, valid_until = ?, is_active = ?\n                WHERE id = ? AND restaurant_id = ?\n            ");
            $stmt->bind_param('idssiii', $menuItemId, $discountPct, $validFrom, $validUntil, $isActive, $id, $restaurantId);
            return $stmt->execute();
        }

        $stmt = $this->db->prepare("\n            INSERT INTO discounts (menu_item_id, restaurant_id, discount_pct, valid_from, valid_until, is_active)\n            VALUES (?, ?, ?, ?, ?, ?)\n        ");
        $stmt->bind_param('iidssi', $menuItemId, $restaurantId, $discountPct, $validFrom, $validUntil, $isActive);
        return $stmt->execute();
    }

    public function toggleDiscount(int $restaurantId, int $discountId): bool {
        $stmt = $this->db->prepare("UPDATE discounts SET is_active = IF(is_active = 1, 0, 1) WHERE id = ? AND restaurant_id = ?");
        $stmt->bind_param('ii', $discountId, $restaurantId);
        return $stmt->execute();
    }

    public function deleteDiscount(int $restaurantId, int $discountId): bool {
        $stmt = $this->db->prepare("DELETE FROM discounts WHERE id = ? AND restaurant_id = ?");
        $stmt->bind_param('ii', $discountId, $restaurantId);
        return $stmt->execute();
    }

    public function salesByPeriod(int $restaurantId, string $period): array {
        $format = '%Y-%m-%d';
        if ($period === 'week') $format = '%x-W%v';
        if ($period === 'month') $format = '%Y-%m';

        $sql = "\n            SELECT DATE_FORMAT(created_at, '$format') AS period_label,\n                   COUNT(*) AS total_orders,\n                   COALESCE(SUM(total_amount), 0) AS total_revenue,\n                   COALESCE(AVG(total_amount), 0) AS avg_order_value\n            FROM orders\n            WHERE restaurant_id = ? AND status <> 'cancelled'\n            GROUP BY period_label\n            ORDER BY period_label DESC\n            LIMIT 12\n        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function mostOrderedItems(int $restaurantId): array {
        $stmt = $this->db->prepare("\n            SELECT mi.name, SUM(oi.quantity) AS total_quantity, COALESCE(SUM(oi.quantity * oi.unit_price), 0) AS item_revenue\n            FROM order_items oi\n            JOIN orders o ON o.id = oi.order_id\n            JOIN menu_items mi ON mi.id = oi.menu_item_id\n            WHERE o.restaurant_id = ? AND o.status <> 'cancelled'\n            GROUP BY mi.id, mi.name\n            ORDER BY total_quantity DESC\n            LIMIT 10\n        ");
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function complaints(int $restaurantId): array {
        $stmt = $this->db->prepare("\n            SELECT c.*, u.name AS submitter_name\n            FROM complaints c\n            JOIN users u ON u.id = c.submitter_id\n            WHERE c.restaurant_id = ?\n               OR EXISTS (SELECT 1 FROM orders o WHERE o.restaurant_id = ? AND o.customer_id = c.submitter_id)\n            ORDER BY c.created_at DESC\n        ");
        $stmt->bind_param('ii', $restaurantId, $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
