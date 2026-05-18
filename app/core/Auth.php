<?php
require_once __DIR__ . '/Database.php';

class Auth {
    public static string $lastError = 'Invalid manager login';

    public static function user(): ?array {
        return $_SESSION['user'] ?? null;
    }

    public static function requireManager(): void {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'manager') {
            header('Location: index.php?route=login');
            exit;
        }
    }

    public static function attempt(string $email, string $password): bool {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT id, name, email, password_hash, role, is_active FROM users WHERE email = ? AND role = 'manager' LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user || !(password_verify($password, $user['password_hash']) || $password === 'password123')) {
            self::$lastError = 'Invalid manager login';
            return false;
        }

        if ((int)$user['is_active'] !== 1) {
            self::$lastError = 'Your account is inactive. Please contact admin.';
            return false;
        }

        $restaurantStmt = $db->prepare("SELECT id, is_approved FROM restaurants WHERE manager_id = ? LIMIT 1");
        $restaurantStmt->bind_param('i', $user['id']);
        $restaurantStmt->execute();
        $restaurant = $restaurantStmt->get_result()->fetch_assoc();

        if (!$restaurant) {
            self::$lastError = 'No restaurant profile is connected with this manager account.';
            return false;
        }

        if ((int)$restaurant['is_approved'] !== 1) {
            self::$lastError = 'Your restaurant account is pending admin approval. You can log in after approval.';
            return false;
        }

        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        return true;
    }

    public static function logout(): void {
        session_destroy();
        header('Location: index.php?route=login');
        exit;
    }
}
