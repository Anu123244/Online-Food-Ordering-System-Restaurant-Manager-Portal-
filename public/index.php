<?php
require_once __DIR__ . '/../app/controllers/LoginController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';

$route = $_GET['route'] ?? 'login';

try {
    switch ($route) {
        case 'login':
            (new LoginController())->show();
            break;
        case 'login_post':
            (new LoginController())->login();
            break;
        case 'register':
            (new LoginController())->register();
            break;
        case 'register_post':
            (new LoginController())->registerPost();
            break;
        case 'logout':
            (new LoginController())->logout();
            break;
        case 'dashboard':
            (new DashboardController())->dashboard();
            break;
        case 'menu':
            (new DashboardController())->menu();
            break;
        case 'category_save':
            (new DashboardController())->saveCategory();
            break;
        case 'category_delete':
            (new DashboardController())->deleteCategory();
            break;
        case 'menu_item_save':
            (new DashboardController())->saveMenuItem();
            break;
        case 'menu_item_delete':
            (new DashboardController())->deleteMenuItem();
            break;
        case 'discounts':
            (new DashboardController())->discounts();
            break;
        case 'discount_save':
            (new DashboardController())->saveDiscount();
            break;
        case 'discount_toggle':
            (new DashboardController())->toggleDiscount();
            break;
        case 'discount_delete':
            (new DashboardController())->deleteDiscount();
            break;
        case 'order_history':
            (new DashboardController())->orderHistory();
            break;
        case 'reviews':
            (new DashboardController())->reviews();
            break;
        case 'review_reply':
            (new DashboardController())->replyReview();
            break;
        case 'profile':
            (new DashboardController())->profile();
            break;
        case 'profile_save':
            (new DashboardController())->saveProfile();
            break;
        case 'analytics':
            (new DashboardController())->analytics();
            break;
        case 'complaints':
            (new DashboardController())->complaints();
            break;
        default:
            http_response_code(404);
            echo '404 Not Found';
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h1>Application Error</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>';
}
