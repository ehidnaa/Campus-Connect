<?php
// 🌸 Login API for Campus Connect
// Checks email + password and starts a session 🧸💻

header('Content-Type: application/json');
require_once '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>'error','html'=>'<div class="alert alert-danger">Invalid request 🌸</div>']);
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];

if ($email === '') $errors[] = 'Please enter your email ✉️';
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address 🌸';

if ($password === '') $errors[] = 'Password cannot be empty 🔐';

if (!empty($errors)) {
    $html = '<div class="alert alert-danger"><ul class="mb-0">';
    foreach ($errors as $e) $html .= "<li>$e</li>";
    $html .= '</ul></div>';
    echo json_encode(['status'=>'error','html'=>$html]);
    exit;
}

// 🌼 Fetch user by email
$stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
    echo json_encode(['status'=>'error','html'=>'<div class="alert alert-danger">Wrong email or password 💔</div>']);
    exit;
}

// 💕 Login successful
$_SESSION['user_id'] = $user['id'];
$_SESSION['name']    = $user['name'];
$_SESSION['role']    = $user['role'];

echo json_encode([
    'status'=>'success',
    'html'=>'<div class="alert alert-success">Welcome back, ' . htmlspecialchars($user['name']) . '! ✨</div>'
]);