<?php
require_once __DIR__ . '/../../app/core/Auth.php';
require_once __DIR__ . '/../../app/models/RestaurantManagerModel.php';

header('Content-Type: application/json');

if (!Auth::user() || Auth::user()['role'] !== 'manager') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$model = new RestaurantManagerModel();
$restaurant = $model->restaurantByManager(Auth::user()['id']);

if (!$restaurant) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Restaurant not found']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';

if ($action === 'list') {
    $orders = $model->incomingOrders((int)$restaurant['id']);
    echo json_encode(['success' => true, 'orders' => $orders]);
    exit;
}

if ($action === 'update_status') {
    $orderId = (int)($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $ok = $model->updateOrderStatus($orderId, (int)$restaurant['id'], $status);
    echo json_encode(['success' => $ok, 'message' => $ok ? 'Order updated' : 'Invalid status']);
    exit;
}

if ($action === 'toggle_item') {
    $itemId = (int)($_POST['item_id'] ?? 0);
    $ok = $model->toggleMenuAvailability($itemId, (int)$restaurant['id']);
    echo json_encode(['success' => $ok, 'message' => $ok ? 'Menu item updated' : 'Failed']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
