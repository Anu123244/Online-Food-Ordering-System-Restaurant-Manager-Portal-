<?php require __DIR__ . '/partials/header.php'; ?>
<section class="panel page-banner">
    <div>
        <h2>Order History</h2>
        <p class="muted">Track every completed, cancelled, and delivered order with customer details.</p>
    </div>
    <img src="assets/images/dashboard-preview.jpg" alt="Order History illustration">
</section>
<section class="panel">
    <h2>Full Order History</h2>
    <p>Shows customer name, ordered items, total amount, and delivery/order status.</p>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Items Ordered</th>
                    <th>Total</th>
                    <th>Delivery Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= (int)$order['id'] ?></td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td><?= htmlspecialchars($order['item_summary'] ?? 'No items') ?></td>
                        <td>৳<?= number_format((float)$order['total_amount'], 2) ?></td>
                        <td><span class="badge"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $order['status']))) ?></span></td>
                        <td><?= htmlspecialchars($order['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
