<?php require __DIR__ . '/partials/header.php'; ?>
<section class="hero hero-with-logo">
    <img src="<?= htmlspecialchars(!empty($restaurant['logo_path']) ? $restaurant['logo_path'] : 'assets/images/logo-mark.svg') ?>" class="restaurant-logo" alt="Restaurant logo">
    <div>
        <h2><?= htmlspecialchars($restaurant['name']) ?></h2>
        <p><?= htmlspecialchars($restaurant['description']) ?></p>
        <p><span class="badge <?= $restaurant['is_open'] ? 'open' : 'closed' ?>"><?= $restaurant['is_open'] ? 'Open' : 'Closed' ?></span> <?= htmlspecialchars($restaurant['cuisine_type'] ?? '') ?> · <?= htmlspecialchars($restaurant['city'] ?? '') ?></p>
    </div>
    <div class="hero-visual">
        <img src="assets/images/hero-food.svg" alt="FoodHub purple restaurant illustration">
    </div>
</section>

<section class="grid cards">
    <div class="card"><span>Total Orders</span><strong><?= (int)$stats['total_orders'] ?></strong></div>
    <div class="card"><span>Active Orders</span><strong><?= (int)$stats['active_orders'] ?></strong></div>
    <div class="card"><span>Total Revenue</span><strong>৳<?= number_format((float)$stats['total_revenue'], 2) ?></strong></div>
    <div class="card"><span>Average Order</span><strong>৳<?= number_format((float)$stats['avg_order_value'], 2) ?></strong></div>
</section>

<section class="panel">
    <div class="section-head">
        <h3>Incoming Orders - Real-Time AJAX</h3>
        <button onclick="loadManagerOrders()">Refresh</button>
    </div>
    <div id="ajaxNotice" class="notice"></div>
    <div id="ordersList" class="orders grouped-orders">
        <?php foreach ($groupedOrders as $status => $statusOrders): ?>
            <div class="status-column">
                <h4><?= ucwords(str_replace('_', ' ', $status === 'ready' ? 'Ready for Pickup' : $status)) ?></h4>
                <?php if (empty($statusOrders)): ?>
                    <p class="muted">No orders</p>
                <?php endif; ?>
                <?php foreach ($statusOrders as $order): ?>
                    <article class="order-card">
                        <h4>Order #<?= (int)$order['id'] ?></h4>
                        <p><b>Customer:</b> <?= htmlspecialchars($order['customer_name']) ?></p>
                        <p><b>Items:</b> <?= htmlspecialchars($order['item_summary'] ?? 'No items') ?></p>
                        <p><b>Address:</b> <?= htmlspecialchars($order['delivery_address']) ?></p>
                        <p><b>Total:</b> ৳<?= number_format((float)$order['total_amount'], 2) ?></p>
                        <div class="actions">
                            <button onclick="updateOrderStatus(<?= (int)$order['id'] ?>, 'accepted')">Accept</button>
                            <button onclick="updateOrderStatus(<?= (int)$order['id'] ?>, 'preparing')">Preparing</button>
                            <button onclick="updateOrderStatus(<?= (int)$order['id'] ?>, 'ready')">Ready for Pickup</button>
                            <button class="danger" onclick="updateOrderStatus(<?= (int)$order['id'] ?>, 'cancelled')">Reject</button>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
