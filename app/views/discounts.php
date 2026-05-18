<?php require __DIR__ . '/partials/header.php'; ?>
<section class="panel page-banner">
    <div>
        <h2>Discount Campaigns</h2>
        <p class="muted">Create limited-time offers and track how many orders used them.</p>
    </div>
    <img src="assets/images/dashboard-preview.jpg" alt="Discount Campaigns illustration">
</section>
<?php if (isset($_GET['success'])): ?><div class="alert success"><?= htmlspecialchars($_GET['success']) ?></div><?php endif; ?>
<?php if (isset($_GET['error'])): ?><div class="alert error"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>

<section class="panel">
    <h2>Create Limited-Time Discount</h2>
    <form method="post" action="index.php?route=discount_save" class="form-grid">
        <label>Menu Item
            <select name="menu_item_id" required>
                <option value="">Select item</option>
                <?php foreach ($items as $item): ?>
                    <option value="<?= (int)$item['id'] ?>"><?= htmlspecialchars($item['name']) ?> - ৳<?= number_format((float)$item['price'], 2) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Discount %<input type="number" step="0.01" min="1" max="100" name="discount_pct" required></label>
        <label>Valid From<input type="datetime-local" name="valid_from" required></label>
        <label>Valid Until<input type="datetime-local" name="valid_until" required></label>
        <label class="checkbox"><input type="checkbox" name="is_active" checked> Active</label>
        <button type="submit">Create Discount</button>
    </form>
</section>

<section class="panel">
    <h2>Discount Campaigns & Performance</h2>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Discount</th>
                    <th>Validity</th>
                    <th>Status</th>
                    <th>Orders Used</th>
                    <th>Items Sold</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($discounts as $discount): ?>
                    <tr>
                        <td><?= htmlspecialchars($discount['item_name']) ?></td>
                        <td><?= number_format((float)$discount['discount_pct'], 2) ?>%</td>
                        <td><?= htmlspecialchars($discount['valid_from']) ?> to <?= htmlspecialchars($discount['valid_until']) ?></td>
                        <td><span class="badge <?= $discount['is_active'] ? 'open' : 'closed' ?>"><?= $discount['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                        <td><?= (int)$discount['orders_used'] ?></td>
                        <td><?= (int)$discount['items_sold_during_campaign'] ?></td>
                        <td>
                            <form method="post" action="index.php?route=discount_toggle" class="inline-small">
                                <input type="hidden" name="id" value="<?= (int)$discount['id'] ?>">
                                <button type="submit"><?= $discount['is_active'] ? 'Deactivate' : 'Activate' ?></button>
                            </form>
                            <form method="post" action="index.php?route=discount_delete" class="inline-small" onsubmit="return confirm('Delete this discount?');">
                                <input type="hidden" name="id" value="<?= (int)$discount['id'] ?>">
                                <button class="danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="muted">Performance counts orders containing the discounted item during the campaign validity period.</p>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
