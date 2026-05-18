<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/RestaurantManagerModel.php';

class DashboardController {
    private RestaurantManagerModel $model;

    public function __construct() {
        Auth::requireManager();
        $this->model = new RestaurantManagerModel();
    }

    private function restaurant(): array {
        $restaurant = $this->model->restaurantByManager(Auth::user()['id']);
        if (!$restaurant) {
            die('No restaurant found for this manager.');
        }
        return $restaurant;
    }

    private function redirect(string $route, string $type, string $message): void {
        header('Location: index.php?route=' . $route . '&' . $type . '=' . urlencode($message));
        exit;
    }

    private function uploadImage(string $field, string $folder, ?string $oldPath = null): ?string {
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
            return $oldPath;
        }
        if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            return $oldPath;
        }

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowed, true)) {
            return $oldPath;
        }

        $directory = __DIR__ . '/../../public/uploads/' . $folder;
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $filename = $field . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $target = $directory . '/' . $filename;
        if (!move_uploaded_file($_FILES[$field]['tmp_name'], $target)) {
            return $oldPath;
        }

        return 'uploads/' . $folder . '/' . $filename;
    }

    public function dashboard(): void {
        $restaurant = $this->restaurant();
        $stats = $this->model->dashboardStats((int)$restaurant['id']);
        $orders = $this->model->incomingOrders((int)$restaurant['id']);
        $groupedOrders = $this->model->groupedActiveOrders((int)$restaurant['id']);
        require __DIR__ . '/../views/dashboard.php';
    }

    public function menu(): void {
        $restaurant = $this->restaurant();
        $items = $this->model->menuItems((int)$restaurant['id']);
        $categories = $this->model->categories((int)$restaurant['id']);
        require __DIR__ . '/../views/menu.php';
    }

    public function saveCategory(): void {
        $restaurant = $this->restaurant();
        $ok = $this->model->saveCategory((int)$restaurant['id'], $_POST);
        $this->redirect('menu', $ok ? 'success' : 'error', $ok ? 'Category saved' : 'Category name is required');
    }

    public function deleteCategory(): void {
        $restaurant = $this->restaurant();
        $ok = $this->model->deleteCategory((int)$restaurant['id'], (int)($_POST['id'] ?? 0));
        $this->redirect('menu', $ok ? 'success' : 'error', $ok ? 'Category deleted' : 'Could not delete category');
    }

    public function saveMenuItem(): void {
        $restaurant = $this->restaurant();
        $existing = null;
        $itemId = (int)($_POST['id'] ?? 0);
        if ($itemId > 0) {
            $existing = $this->model->menuItemById($itemId, (int)$restaurant['id']);
        }

        $imagePath = $this->uploadImage('image', 'menu', $existing['image_path'] ?? null);
        $data = $_POST;
        $data['image_path'] = $imagePath;
        $ok = $this->model->saveMenuItem((int)$restaurant['id'], $data);
        $this->redirect('menu', $ok ? 'success' : 'error', $ok ? 'Menu item saved' : 'Menu item name and valid price are required');
    }

    public function deleteMenuItem(): void {
        $restaurant = $this->restaurant();
        $ok = $this->model->deleteMenuItem((int)$restaurant['id'], (int)($_POST['id'] ?? 0));
        $this->redirect('menu', $ok ? 'success' : 'error', $ok ? 'Menu item deleted' : 'Could not delete item. It may be used in existing orders.');
    }

    public function discounts(): void {
        $restaurant = $this->restaurant();
        $items = $this->model->menuItems((int)$restaurant['id']);
        $discounts = $this->model->discounts((int)$restaurant['id']);
        require __DIR__ . '/../views/discounts.php';
    }

    public function saveDiscount(): void {
        $restaurant = $this->restaurant();
        $ok = $this->model->saveDiscount((int)$restaurant['id'], $_POST);
        $this->redirect('discounts', $ok ? 'success' : 'error', $ok ? 'Discount campaign saved' : 'Please complete all discount fields correctly');
    }

    public function toggleDiscount(): void {
        $restaurant = $this->restaurant();
        $ok = $this->model->toggleDiscount((int)$restaurant['id'], (int)($_POST['id'] ?? 0));
        $this->redirect('discounts', $ok ? 'success' : 'error', $ok ? 'Discount status updated' : 'Could not update discount');
    }

    public function deleteDiscount(): void {
        $restaurant = $this->restaurant();
        $ok = $this->model->deleteDiscount((int)$restaurant['id'], (int)($_POST['id'] ?? 0));
        $this->redirect('discounts', $ok ? 'success' : 'error', $ok ? 'Discount deleted' : 'Could not delete discount');
    }

    public function orderHistory(): void {
        $restaurant = $this->restaurant();
        $orders = $this->model->orderHistory((int)$restaurant['id']);
        require __DIR__ . '/../views/order_history.php';
    }

    public function reviews(): void {
        $restaurant = $this->restaurant();
        $reviews = $this->model->reviews((int)$restaurant['id']);
        require __DIR__ . '/../views/reviews.php';
    }

    public function replyReview(): void {
        $restaurant = $this->restaurant();
        $reply = trim($_POST['manager_reply'] ?? '');
        $ok = $this->model->updateReviewReply((int)$restaurant['id'], (int)($_POST['review_id'] ?? 0), $reply);
        $this->redirect('reviews', $ok ? 'success' : 'error', $ok ? 'Reply saved' : 'Could not save reply');
    }

    public function profile(): void {
        $restaurant = $this->restaurant();
        require __DIR__ . '/../views/profile.php';
    }

    public function saveProfile(): void {
        $restaurant = $this->restaurant();
        $logoPath = $this->uploadImage('logo', 'logos', $restaurant['logo_path'] ?? null);
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'cuisine_type' => trim($_POST['cuisine_type'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'logo_path' => $logoPath,
            'opening_hours' => trim($_POST['opening_hours'] ?? ''),
            'delivery_radius_km' => (float)($_POST['delivery_radius_km'] ?? 0),
            'is_open' => isset($_POST['is_open']) ? 1 : 0
        ];

        if ($data['name'] === '' || $data['city'] === '') {
            $this->redirect('profile', 'error', 'Restaurant name and city are required');
        }

        $this->model->updateProfile((int)$restaurant['id'], $data);
        $this->redirect('profile', 'success', 'Profile updated');
    }

    public function analytics(): void {
        $restaurant = $this->restaurant();
        $stats = $this->model->dashboardStats((int)$restaurant['id']);
        $daily = $this->model->salesByPeriod((int)$restaurant['id'], 'day');
        $weekly = $this->model->salesByPeriod((int)$restaurant['id'], 'week');
        $monthly = $this->model->salesByPeriod((int)$restaurant['id'], 'month');
        $popularItems = $this->model->mostOrderedItems((int)$restaurant['id']);
        require __DIR__ . '/../views/analytics.php';
    }

    public function complaints(): void {
        $restaurant = $this->restaurant();
        $complaints = $this->model->complaints((int)$restaurant['id']);
        require __DIR__ . '/../views/complaints.php';
    }
}
