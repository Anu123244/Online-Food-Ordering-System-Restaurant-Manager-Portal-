<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Manager Login - FoodHub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
    <div class="auth-layout">
        <section class="auth-visual">
            <div>
                <img src="assets/images/brand-logo.svg" alt="FoodHub Manager logo" style="width:260px; box-shadow:none; margin:0;">
                <h2>Run your restaurant beautifully.</h2>
                <p>Manage orders, menu items, discounts, reviews, and performance analytics from one clean purple-and-white dashboard.</p>
            </div>
            <img src="assets/images/hero-food.svg" alt="Restaurant food illustration">
        </section>
        <section class="login-card">
            <img src="assets/images/logo-mark.svg" class="brand-login" alt="FoodHub mark" style="width:86px;">
            <h1>Restaurant Manager Login</h1>
            <p class="muted">Approved managers can log in and operate their restaurant.</p>
            <?php if (!empty($success)): ?><div class="alert success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
            <?php if (!empty($error)): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <form method="post" action="index.php?route=login_post">
                <label>Email<input type="email" name="email" value="manager@foodhub.test" required></label>
                <label>Password<input type="password" name="password" value="password123" required></label>
                <button type="submit">Login</button>
            </form>
            <p class="auth-link">New restaurant? <a href="index.php?route=register">Register and submit for admin approval</a></p>
        </section>
    </div>
</body>
</html>
