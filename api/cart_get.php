<?php
// 🌸 Cart Get API
// Returns a small summary: how many items and total price 💸

header('Content-Type: application/json');

session_start();

$itemsCount = 0;
$totalPrice = 0.0;

if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $row) {
        $qty   = (int)$row['qty'];
        $price = (float)$row['price'];
        $itemsCount += $qty;
        $totalPrice += $qty * $price;
    }
}

if ($itemsCount === 0) {
    $html = '
        <div class="alert alert-info mb-0">
            Your cart is empty 💔 Go add something cute from the merch list!
        </div>
    ';
} else {
    $html = '
        <div class="alert alert-success mb-0">
            Cart: <strong>' . $itemsCount . ' item(s)</strong> · Total: 
            <strong>€' . number_format($totalPrice, 2) . '</strong> 🧺
            <a href="checkout.php" class="ms-2 btn btn-sm btn-light">Go to checkout →</a>
        </div>
    ';
}

echo json_encode([
    'status' => 'success',
    'html'   => $html
]);