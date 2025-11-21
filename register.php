<?php include 'header.php'; ?>

<h1 class="mb-4">Create your Campus Connect account 🌸</h1>

<div id="registerMessage"></div>

<form id="registerForm" class="card p-4 shadow-sm bg-white">
    <div class="mb-3">
        <label class="form-label">Full name</label>
        <input type="text" class="form-control" name="name" placeholder="Cute Student Name 💕">
    </div>

    <div class="mb-3">
        <label class="form-label">ATU Email</label>
        <input type="email" class="form-control" name="email" placeholder="you@example.com">
    </div>

    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" class="form-control" name="password" placeholder="••••••">
    </div>

    <div class="mb-3">
        <label class="form-label">Confirm password</label>
        <input type="password" class="form-control" name="confirm" placeholder="repeat your password">
    </div>

    <button class="btn btn-primary">Sign up ✨</button>
</form>

<script>
// 🌸 Using fetch() to send registration data to the backend
document.getElementById('registerForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    const box = document.getElementById('registerMessage');

    try {
        const res  = await fetch('api/register_user.php', { method:'POST', body:formData });
        const data = await res.json();
        box.innerHTML = data.html;

        if (data.status === 'success') e.target.reset();

    } catch (err) {
        box.innerHTML = '<div class="alert alert-danger">Server error 💔</div>';
    }
});
</script>

<?php include 'footer.php'; ?>