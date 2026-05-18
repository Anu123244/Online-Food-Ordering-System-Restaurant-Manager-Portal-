<?php require __DIR__ . '/partials/header.php'; ?>
<section class="panel page-banner">
    <div>
        <h2>Menu Management</h2>
        <p class="muted">Add colorful menu pictures, organize categories, and keep availability updated.</p>
    </div>
    <img src="assets/images/hero-food.svg" alt="Menu Management illustration">
</section>
<?php if (isset($_GET['success'])): ?><div class="alert success"><?= htmlspecialchars($_GET['success']) ?></div><?php endif; ?>
<?php if (isset($_GET['error'])): ?><div class="alert error"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>

<section class="panel">
    <h2>Menu Categories</h2>
    <p>Create, rename, reorder, and delete menu categories.</p>
    <form method="post" action="index.php?route=category_save" class="inline-form">
        <input name="name" placeholder="New category name" required>
        <input type="number" name="display_order" placeholder="Order" value="1">
        <button type="submit">Add Category</button>
    </form>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Display Order</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td colspan="3">
                            <form method="post" action="index.php?route=category_save" class="table-form">
                                <input type="hidden" name="id" value="<?= (int)$category['id'] ?>">
                                <input name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
                                <input type="number" name="display_order" value="<?= (int)$category['display_order'] ?>">
                                <button type="submit">Save</button>
                            </form>
                            <form method="post" action="index.php?route=category_delete" class="delete-form" onsubmit="return confirm('Delete this category? Items will become uncategorized.');">
                                <input type="hidden" name="id" value="<?= (int)$category['id'] ?>">
                                <button class="danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <h2>Add Menu Item</h2>
    <form method="post" enctype="multipart/form-data" action="index.php?route=menu_item_save" class="form-grid">
        <label>Name<input name="name" required></label>
        <label>Category
            <select name="category_id">
                <option value="">Uncategorized</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int)$category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Price<input type="number" step="0.01" name="price" required></label>
        <label>Image<input type="file" name="image" accept="image/*"></label>
        <label class="wide">Description<textarea name="description"></textarea></label>
        <label class="checkbox"><input type="checkbox" name="is_available" checked> Available</label>
        <button type="submit">Add Item</button>
    </form>
</section>

<section class="panel">
    <h2>Manage Menu Items</h2>
    <p>Add, edit, delete, upload images, assign categories, and toggle item availability.</p>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Item Details</th>
                    <th>Category / Price</th>
                    <th>Availability</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <img src="<?= htmlspecialchars(!empty($item['image_path']) ? $item['image_path'] : 'assets/images/hero-food.svg') ?>" class="thumb" alt="Menu item">
                        </td>
                        <td colspan="4">
                            <form method="post" enctype="multipart/form-data" action="index.php?route=menu_item_save" class="item-edit-form">
                                <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                <div class="form-grid compact-grid">
                                    <label>Name<input name="name" value="<?= htmlspecialchars($item['name']) ?>" required></label>
                                    <label>Category
                                        <select name="category_id">
                                            <option value="">Uncategorized</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?= (int)$category['id'] ?>" <?= (int)$item['category_id'] === (int)$category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>
                                    <label>Price<input type="number" step="0.01" name="price" value="<?= htmlspecialchars($item['price']) ?>" required></label>
                                    <label>Replace Image<input type="file" name="image" accept="image/*"></label>
                                    <label class="wide">Description<textarea name="description"><?= htmlspecialchars($item['description']) ?></textarea></label>
                                    <label class="checkbox"><input type="checkbox" name="is_available" <?= $item['is_available'] ? 'checked' : '' ?>> Available</label>
                                </div>
                                <div class="actions">
                                    <button type="submit">Save Item</button>
                                    <button type="button" onclick="toggleMenuItem(<?= (int)$item['id'] ?>)"></button>
                                </div>
                            </form>
                            <form method="post" action="index.php?route=menu_item_delete" class="delete-form" onsubmit="return confirm('Delete this menu item?');">
                                <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                <button class="danger" type="submit">Delete Item</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
