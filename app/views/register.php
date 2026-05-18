<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Register Restaurant - FoodHub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page register-bg">
    <section class="login-card wide-card">
        <img src="assets/images/brand-logo.svg" class="brand-login" alt="FoodHub Manager logo">
        <h1>Register Restaurant Account</h1>
        <p class="muted">Submit restaurant details. The account remains pending until admin approval.</p>
        <?php if (!empty($success)): ?><div class="alert success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
        <?php if (!empty($error)): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <form method="post" action="index.php?route=register_post" class="form-grid">
            <label>Manager Name<input name="manager_name" required></label>
            <label>Email<input type="email" name="email" required></label>
            <label>Password<input type="password" name="password" required></label>
            <label>Phone<input name="phone"></label>
            <label>Restaurant Name<input name="restaurant_name" required></label>
            <label>Cuisine Type<input name="cuisine_type" placeholder="Fast Food, Bengali, Chinese"></label>
            <label>Address<input name="address"></label>
            <label>City<input name="city" required></label>
            <label>Opening Hours<input name="opening_hours" placeholder="10:00 AM - 11:00 PM"></label>
            <label>Delivery Radius KM<input type="number" step="0.01" name="delivery_radius_km" value="5"></label>
            <label class="wide">Description<textarea name="description"></textarea></label>
            <button type="submit">Submit for Approval</button>
        </form>
        <p class="auth-link"><a href="index.php?route=login">Back to login</a></p>
    </section>
</body>
</html>
