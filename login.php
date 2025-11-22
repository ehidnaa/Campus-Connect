<?php include 'header.php'; ?>

<h1 class="mb-4">Log in to Campus Connect 💻🌸</h1>

<div id="loginMessage"></div>

<form id="loginForm" class="card p-4 shadow-sm bg-white">
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email">
    </div>

    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" class="form-control" name="password">
    </div>

    <button class="btn btn-primary">Log in ✨</button>
</form>

<script>
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    const box = document.getElementById('loginMessage');

    try {
        const res = await fetch('api/login_user.php', { method:'POST', body:formData });
        const data = await res.json();
        box.innerHTML = data.html;

        if (data.status === 'success') {
            setTimeout(()=> window.location.href='index.php', 1000);
        }
    } catch (err) {
        box.innerHTML = '<div class="alert alert-danger">Login error 💔</div>';
    }
});
</script>

<?php include 'footer.php'; ?>