<?php
// 🌸 Merch page: shows cute campus items and lets students add them to a cart
include 'header.php';
?>

<h1 class="mb-3">Campus Merch Shop 🛍️</h1>
<p class="text-muted mb-3">
    Grab some hoodies, mugs and stickers to show your campus spirit 💚
</p>

<img src="images/merch-placeholder.jpg"
     alt="Campus merchandise"
     class="img-fluid rounded mb-4 w-100"
     style="max-height:260px; object-fit:cover;">

<div id="merchCartSummary" class="mb-3">
    <!-- 🌸 Cart summary will be loaded here -->
</div>

<div id="merchMessage"></div>

<div id="merchList">
    <div class="text-center text-muted py-4">Loading merch… ⏳</div>
</div>

<script>
// 🌸 Load merch items from the backend
async function loadMerch() {
    const list = document.getElementById('merchList');

    try {
        const res  = await fetch('api/get_merch.php');
        const data = await res.json();
        list.innerHTML = data.html || '';

    } catch (err) {
        console.error('Error loading merch', err);
        list.innerHTML = '<div class="alert alert-danger">Could not load merch 💔</div>';
    }
}

// 🌸 Load cart summary (items count + total price)
async function loadCartSummary() {
    const box = document.getElementById('merchCartSummary');

    try {
        const res  = await fetch('api/cart_get.php');
        const data = await res.json();
        box.innerHTML = data.html || '';

    } catch (err) {
        console.error('Cart summary error', err);
        box.innerHTML = '<div class="alert alert-danger">Could not load cart summary 💔</div>';
    }
}

loadMerch();
loadCartSummary();

// 🌼 Handle "Add to cart" buttons using event delegation
document.getElementById('merchList').addEventListener('click', async (e) => {
    if (!e.target.classList.contains('merch-add-btn')) return;

    const merchId = e.target.getAttribute('data-merch-id');
    const msgBox  = document.getElementById('merchMessage');

    const formData = new FormData();
    formData.append('merch_id', merchId);

    try {
        const res  = await fetch('api/cart_add.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        msgBox.innerHTML = data.html || '';
        if (data.status === 'success') {
            loadCartSummary();
        }

    } catch (err) {
        console.error('Add to cart error', err);
        msgBox.innerHTML = '<div class="alert alert-danger">Could not add item to cart 💔</div>';
    }
});
</script>

<?php
include 'footer.php';
?>