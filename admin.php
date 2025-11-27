<?php
// 🌸 Admin dashboard
// This page gives a tiny overview of what is happening in the system 💻

include 'header.php';
require_once 'db.php';

// 💕 Security check: only admins allowed here
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    echo '<div class="alert alert-danger mt-3">
            Access denied 🌧 Only admin users can view this page.
          </div>';
    include 'footer.php';
    exit;
}

// 🌼 Collect summary numbers
$stats = [
    'users'         => 0,
    'events'        => 0,
    'registrations' => 0,
    'orders'        => 0,
    'reviews'       => 0,
];

try {
    $stats['users']         = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $stats['events']        = (int)$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
    $stats['registrations'] = (int)$pdo->query("SELECT COUNT(*) FROM registrations")->fetchColumn();
    $stats['orders']        = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $stats['reviews']       = (int)$pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
} catch (Exception $e) {
    // 🌧 If something goes wrong, we just show zeros, it is still fine
}
?>

<h1 class="mb-3 mt-3">Admin Dashboard 🌸</h1>
<p class="text-muted mb-4">
    A quick overview of users, events, registrations, orders and reviews 💚
</p>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-title text-muted">Users</h6>
                <p class="display-6 mb-0"><?= $stats['users'] ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-title text-muted">Events</h6>
                <p class="display-6 mb-0"><?= $stats['events'] ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-title text-muted">Registrations</h6>
                <p class="display-6 mb-0"><?= $stats['registrations'] ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-title text-muted">Orders</h6>
                <p class="display-6 mb-0"><?= $stats['orders'] ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-title text-muted">Reviews</h6>
                <p class="display-6 mb-0"><?= $stats['reviews'] ?></p>
            </div>
        </div>
    </div>
</div>

<?php
// 🌼 Latest event registrations
$registrations = [];
$orders = [];

try {
    $regStmt = $pdo->query("
        SELECT 
            r.registered_at,
            u.name AS user_name,
            e.title AS event_title
        FROM registrations r
        JOIN users u  ON u.id = r.user_id
        JOIN events e ON e.id = r.event_id
        ORDER BY r.registered_at DESC
        LIMIT 10
    ");
    $registrations = $regStmt->fetchAll(PDO::FETCH_ASSOC);

    $ordStmt = $pdo->query("
        SELECT 
            o.created_at,
            o.total_price,
            u.name AS user_name
        FROM orders o
        JOIN users u ON u.id = o.user_id
        ORDER BY o.created_at DESC
        LIMIT 10
    ");
    $orders = $ordStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    // 🌧 If something fails, we just keep arrays empty
}
?>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <h2 class="h5 mb-3">Latest registrations 🌼</h2>
        <?php if (empty($registrations)): ?>
            <div class="alert alert-info">No registrations yet 💭</div>
        <?php else: ?>
            <table class="table table-sm table-striped align-middle">
                <thead>
                <tr>
                    <th>When</th>
                    <th>Student</th>
                    <th>Event</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($registrations as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars(date('d M Y H:i', strtotime($row['registered_at']))) ?></td>
                        <td><?= htmlspecialchars($row['user_name']) ?></td>
                        <td><?= htmlspecialchars($row['event_title']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="col-md-6">
        <h2 class="h5 mb-3">Latest orders 🧺</h2>
        <?php if (empty($orders)): ?>
            <div class="alert alert-info">No orders yet 💭</div>
        <?php else: ?>
            <table class="table table-sm table-striped align-middle">
                <thead>
                <tr>
                    <th>When</th>
                    <th>Student</th>
                    <th>Total (€)</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars(date('d M Y H:i', strtotime($row['created_at']))) ?></td>
                        <td><?= htmlspecialchars($row['user_name']) ?></td>
                        <td><?= number_format($row['total_price'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php
include 'footer.php';
?>