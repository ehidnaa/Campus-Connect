<?php
// 🌸 Checkout API
// Creates an order from the current session cart and clears the cart 💸

header('Content-Type: application/json');

require_once '../db.php';
session_start();

// 💕 User must be logged in to place an order
if (empty($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'html'   => '<div class="alert alert-warning">Please log in to place an order 💻</div>'
    ]);
    exit;
}

// 💕 Cart must not be empty
if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    echo json_encode([
        'status' => 'error',
        'html'   => '<div class="alert alert-info">Your cart is already empty 💔</div>'
    ]);
    exit;
}

// 🌼 Calculate total
$total = 0.0;
foreach ($_SESSION['cart'] as $row) {
    $qty   = (int)$row['qty'];
    $price = (float)$row['price'];
    $total += $qty * $price;
}

if ($total <= 0) {
    echo json_encode([
        'status' => 'error',
        'html'   => '<div class="alert alert-danger">Invalid cart total 🌧</div>'
    ]);
    exit;
}

try {
    $userId = (int)$_SESSION['user_id'];

    // 💕 Insert order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
    $stmt->execute([$userId, $total]);

    // 🌸 Clear the cart after successful order
    $_SESSION['cart'] = [];

    echo json_encode([
        'status' => 'success',
        'html'   => '<div class="alert alert-success">Your order has been placed! Thank you 💚</div>'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'html'   => '<div class="alert alert-danger">Could not complete your order right now 💔</div>'
    ]);
}