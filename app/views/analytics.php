<?php require __DIR__ . '/partials/header.php'; ?>
<section class="panel page-banner">
    <div>
        <h2>Sales Analytics</h2>
        <p class="muted">Review revenue, order volume, average order value, and top-selling items.</p>
    </div>
    <img src="assets/images/dashboard-preview.jpg" alt="Sales Analytics illustration">
</section>
<section class="hero">
    <h2>Sales Analytics</h2>
    <p>Monitor orders, revenue, popular items, and average order value.</p>
</section>

<section class="grid cards">
    <div class="card"><span>Total Orders</span><strong><?= (int)$stats['total_orders'] ?></strong></div>
    <div class="card"><span>Total Revenue</span><strong>৳<?= number_format((float)$stats['total_revenue'], 2) ?></strong></div>
    <div class="card"><span>Average Order Value</span><strong>৳<?= number_format((float)$stats['avg_order_value'], 2) ?></strong></div>
</section>

<?php
function renderAnalyticsTable(string $title, array $rows): void { ?>
<section class="panel">
    <h3><?= htmlspecialchars($title) ?></h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Period</th><th>Orders</th><th>Revenue</th><th>Average Order</th></tr></thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['period_label']) ?></td>
                        <td><?= (int)$row['total_orders'] ?></td>
                        <td>৳<?= number_format((float)$row['total_revenue'], 2) ?></td>
                        <td>৳<?= number_format((float)$row['avg_order_value'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php } ?>

<?php renderAnalyticsTable('Daily Orders & Revenue', $daily); ?>
<?php renderAnalyticsTable('Weekly Orders & Revenue', $weekly); ?>
<?php renderAnalyticsTable('Monthly Orders & Revenue', $monthly); ?>

<section class="panel">
    <h3>Most Ordered Items</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Item</th><th>Total Quantity Ordered</th><th>Item Revenue</th></tr></thead>
            <tbody>
                <?php foreach ($popularItems as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= (int)$item['total_quantity'] ?></td>
                        <td>৳<?= number_format((float)$item['item_revenue'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
