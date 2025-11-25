<?php
// 🌸 Checkout page: shows all items in the cart and lets the user place an order
include 'header.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo '<div class="alert alert-info">Your cart is empty 💔</div>';
    include 'footer.php';
    exit;
}

// 💕 Calculate totals
$items  = $_SESSION['cart'];
$total  = 0.0;
foreach ($items as $row) {
    $total += (float)$row['price'] * (int)$row['qty'];
}
?>

<h1 class="mb-3">Checkout 🧺</h1>

<p class="text-muted">Review your cart and confirm your order ✨</p>

<table class="table table-striped align-middle">
    <thead>
    <tr>
        <th>Item</th>
        <th>Price</th>
        <th>Qty</th>
        <th>Subtotal</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td>€<?= number_format($row['price'], 2) ?></td>
            <td><?= (int)$row['qty'] ?></td>
            <td>€<?= number_format($row['price'] * $row['qty'], 2) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="3" class="text-end">Total:</th>
        <th>€<?= number_format($total, 2) ?></th>
    </tr>
    </tfoot>
</table>

<div id="checkoutMessage"></div>

<form id="checkoutForm" class="mt-3 card p-3">
    <p class="mb-2">
        When you confirm, your order will be saved and your cart will be cleared 💚
    </p>
    <button class="btn btn-success">Confirm order ✨</button>
</form>

<script>
// 🌸 Handle checkout with fetch + JSON
document.getElementById('checkoutForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const msgBox = document.getElementById('checkoutMessage');

    try {
        const res  = await fetch('api/do_checkout.php', { method: 'POST' });
        const data = await res.json();

        msgBox.innerHTML = data.html || '';

        if (data.status === 'success') {
            // 🌼 after success, reload page to show empty cart
            setTimeout(() => {
                window.location.href = 'merch.php';
            }, 1200);
        }
    } catch (err) {
        console.error('Checkout error', err);
        msgBox.innerHTML = '<div class="alert alert-danger">Could not place your order 💔</div>';
    }
});
</script>

<?php
include 'footer.php';
?>