<?php require __DIR__ . '/partials/header.php'; ?>
<section class="panel page-banner">
    <div>
        <h2>Restaurant Profile</h2>
        <p class="muted">Keep restaurant details, logo, delivery radius, and open/closed status updated.</p>
    </div>
    <img src="assets/images/hero-food.svg" alt="Restaurant Profile illustration">
</section>
<section class="panel">
    <h2>Restaurant Profile</h2>
    <?php if (isset($_GET['success'])): ?><div class="alert success"><?= htmlspecialchars($_GET['success']) ?></div><?php endif; ?>
    <?php if (isset($_GET['error'])): ?><div class="alert error"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>

    <p><img src="<?= htmlspecialchars(!empty($restaurant['logo_path']) ? $restaurant['logo_path'] : 'assets/images/logo-mark.svg') ?>" class="profile-logo" alt="Restaurant logo"></p>

    <form method="post" enctype="multipart/form-data" action="index.php?route=profile_save" class="form-grid">
        <label>Name<input name="name" value="<?= htmlspecialchars($restaurant['name']) ?>" required></label>
        <label>Cuisine<input name="cuisine_type" value="<?= htmlspecialchars($restaurant['cuisine_type']) ?>"></label>
        <label>Address<input name="address" value="<?= htmlspecialchars($restaurant['address']) ?>"></label>
        <label>City<input name="city" value="<?= htmlspecialchars($restaurant['city']) ?>" required></label>
        <label>Logo Upload<input type="file" name="logo" accept="image/*"></label>
        <label>Opening Hours<input name="opening_hours" value="<?= htmlspecialchars($restaurant['opening_hours']) ?>"></label>
        <label>Delivery Radius KM<input type="number" step="0.01" name="delivery_radius_km" value="<?= htmlspecialchars($restaurant['delivery_radius_km']) ?>"></label>
        <label class="wide">Description<textarea name="description"><?= htmlspecialchars($restaurant['description']) ?></textarea></label>
        <label class="checkbox"><input type="checkbox" name="is_open" <?= $restaurant['is_open'] ? 'checked' : '' ?>> Restaurant is open</label>
        <button type="submit">Save Profile</button>
    </form>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
