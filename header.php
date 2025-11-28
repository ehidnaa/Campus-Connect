<?php
// 💖 Starting a session so I can remember who's logged in!
session_start();
// 🌸 Handle theme switching via cookie
if (isset($_POST['theme'])) {
    $newTheme = ($_POST['theme'] === 'dark') ? 'dark' : 'light';
    setcookie('theme', $newTheme, time() + 60*60*24*30, '/'); // 30 days
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$currentTheme = $_COOKIE['theme'] ?? 'light';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Connect 🌸</title>

    <!-- 🎀 Responsive design magic -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- 🌼 Bootstrap CSS for beautiful styling out of the box -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- 🌸 My custom CSS -->
    <link rel="stylesheet" href="css/style.css">

    <!-- 🍬 Cute little favicon (I'll add image later) -->
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
</head>

<!-- 💕 light background because it's aesthetic -->
<body class="<?= ($currentTheme === 'dark') ? 'bg-dark text-white' : 'bg-light text-dark' ?>">

<!-- 🌸 Navigation bar (very important!) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">

        <!-- 🎀 Logo / Home button -->
        <a class="navbar-brand" href="index.php">Campus Connect</a>

        <!-- 🌼 Mobile menu button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- 💗 The actual menu -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto">

                <!-- 🌸 Everyone can see these -->
                <li class="nav-item"><a class="nav-link" href="events.php">Events</a></li>
                <li class="nav-item"><a class="nav-link" href="merch.php">Merch</a></li>

                <?php if (!isset($_SESSION['user_id'])): ?>
                    <!-- 🎀 Buttons for guests -->
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>

                <?php else: ?>
                    <!-- 🍬 Buttons for logged-in users -->
                    <li class="nav-item"><a class="nav-link" href="checkout.php">Checkout</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>

                    <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <!-- 🌟 Admin-only button -->
                        <li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <form method="POST" class="d-flex ms-3">
    <input type="hidden" name="theme" value="<?= ($currentTheme === 'dark') ? 'light' : 'dark' ?>">
    <button class="btn btn-sm <?= ($currentTheme === 'dark') ? 'btn-light' : 'btn-dark' ?>">
        <?= ($currentTheme === 'dark') ? 'Light Mode 🌞' : 'Dark Mode 🌙' ?>
    </button>
</form>
</nav>

<!-- 🌸 Cute little wrapper for each page -->
<div class="container mt-4">