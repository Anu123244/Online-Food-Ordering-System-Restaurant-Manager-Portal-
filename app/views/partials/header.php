<?php $user = Auth::user(); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>FoodHub Manager - Restaurant Manager</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="topbar">
    <a href="index.php?route=dashboard" class="brand" aria-label="FoodHub Manager dashboard">
        <img src="assets/images/brand-logo.svg" alt="FoodHub Manager logo">
        <div class="brand-copy">
            <h1>FoodHub</h1>
            <p>Restaurant Manager</p>
        </div>
    </a>
    <?php if ($user): ?>
        <nav>
            <a href="index.php?route=dashboard">Dashboard</a>
            <a href="index.php?route=menu">Menu</a>
            <a href="index.php?route=discounts">Discounts</a>
            <a href="index.php?route=order_history">Order History</a>
            <a href="index.php?route=analytics">Analytics</a>
            <a href="index.php?route=reviews">Reviews</a>
            <a href="index.php?route=complaints">Complaints</a>
            <a href="index.php?route=profile">Profile</a>
            <a href="index.php?route=logout">Logout</a>
        </nav>
    <?php endif; ?>
</header>
<main class="container">
