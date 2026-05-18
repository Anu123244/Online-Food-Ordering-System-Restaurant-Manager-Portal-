<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/RestaurantManagerModel.php';

class LoginController {
    public function show(): void {
        $error = $_GET['error'] ?? '';
        $success = $_GET['success'] ?? '';
        require __DIR__ . '/../views/login.php';
    }

    public function login(): void {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            header('Location: index.php?route=login&error=' . urlencode('Email and password are required'));
            exit;
        }

        if (Auth::attempt($email, $password)) {
            header('Location: index.php?route=dashboard');
            exit;
        }

        header('Location: index.php?route=login&error=' . urlencode(Auth::$lastError));
        exit;
    }

    public function register(): void {
        $error = $_GET['error'] ?? '';
        $success = $_GET['success'] ?? '';
        require __DIR__ . '/../views/register.php';
    }

    public function registerPost(): void {
        $model = new RestaurantManagerModel();
        $result = $model->createManagerRegistration($_POST);
        if ($result['success']) {
            header('Location: index.php?route=login&success=' . urlencode($result['message']));
            exit;
        }
        header('Location: index.php?route=register&error=' . urlencode($result['message']));
        exit;
    }

    public function logout(): void {
        Auth::logout();
    }
}
