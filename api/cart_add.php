<?php
// 🌸 Cart Add API
// Adds one merch item to the session cart 🧺

header('Content-Type: application/json');

require_once '../db.php';
session_start();

$merchId = isset($_POST['merch_id']) ? (int)$_POST['merch_id'] : 0;

if ($merchId <= 0) {
    echo json_encode([
        'status' => 'error',
        'html'   => '<div class="alert alert-danger">Invalid merch item 🌧</div>'
    ]);
    exit;
}

try {
    // 🌼 Fetch merch item details
    $stmt = $pdo->prepare("SELECT id, name, price FROM merch WHERE id = ?");
    $stmt->execute([$merchId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        echo json_encode([
            'status' => 'error',
            'html'   => '<div class="alert alert-danger">This merch item does not exist anymore 💔</div>'
        ]);
        exit;
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // 💕 If item already in cart, increase quantity
    if (isset($_SESSION['cart'][$merchId])) {
        $_SESSION['cart'][$merchId]['qty'] += 1;
    } else {
        $_SESSION['cart'][$merchId] = [
            'id'    => $item['id'],
            'name'  => $item['name'],
            'price' => (float)$item['price'],
            'qty'   => 1
        ];
    }

    echo json_encode([
        'status' => 'success',
        'html'   => '<div class="alert alert-success">Added to cart: ' 
                    . htmlspecialchars($item['name']) . ' 🛍️</div>'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'html'   => '<div class="alert alert-danger">Could not add to cart right now 💔</div>'
    ]);
}