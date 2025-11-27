<?php
include 'header.php';

$isLoggedIn = !empty($_SESSION['user_id']);
$isAdmin    = ($isLoggedIn && ($_SESSION['role'] ?? '') === 'admin');
$name       = $isLoggedIn ? ($_SESSION['name'] ?? 'student') : null;
?>

<div class="p-5 mb-4 bg-white rounded-3 shadow-sm mt-3">
    <div class="container-fluid py-5">
        <?php if ($isLoggedIn): ?>
            <h1 class="display-5 fw-bold">Welcome back, <?= htmlspecialchars($name) ?> 🌸</h1>
            <p class="col-md-8 fs-5">
                Use Campus Connect to explore events, grab some merch and see what other students think 💬
            </p>
        <?php else: ?>
            <h1 class="display-5 fw-bold">Welcome to Campus Connect 🌸</h1>
            <p class="col-md-8 fs-5">
                A small student hub for events, merch and campus activity. Create an account to get started ✨
            </p>
        <?php endif; ?>

        <div class="d-flex flex-wrap gap-2 mt-3">
            <a href="events.php" class="btn btn-primary btn-lg">
                View events 🌼
            </a>
            <a href="merch.php" class="btn btn-outline-primary btn-lg">
                Visit merch shop 🛍️
            </a>

            <?php if (!$isLoggedIn): ?>
                <a href="register.php" class="btn btn-outline-secondary btn-lg">
                    Create account 💕
                </a>
            <?php endif; ?>

            <?php if ($isAdmin): ?>
                <a href="admin.php" class="btn btn-warning btn-lg">
                    Go to admin dashboard ⚙️
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>