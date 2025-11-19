<?php
// 🌸 Registration API for Campus Connect
// This file handles the "Sign up" form using fetch() + JSON 💌

header('Content-Type: application/json');

// 💕 Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'html'   => '<div class="alert alert-danger">Invalid request method 🌸</div>'
    ]);
    exit;
}

require_once '../db.php';
session_start();

// 🌼 Collecting and trimming all the input fields
$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm'] ?? '';

$errors = [];

// 💗 Simple validation rules
if ($name === '') {
    $errors[] = 'Please enter your name 💕';
}

if ($email === '') {
    $errors[] = 'Please enter your email 🌸';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address ✉️';
}

if ($password === '' || $confirm === '') {
    $errors[] = 'Please enter and confirm your password 🔐';
} elseif ($password !== $confirm) {
    $errors[] = 'Passwords do not match, try again 💔';
} elseif (strlen($password) < 6) {
    $errors[] = 'Password should be at least 6 characters long ✨';
}

// 🌼 If there are validation errors, send them back as HTML
if (!empty($errors)) {
    $html = '<div class="alert alert-danger"><ul class="mb-0">';
    foreach ($errors as $err) {
        $html .= '<li>' . htmlspecialchars($err) . '</li>';
    }
    $html .= '</ul></div>';

    echo json_encode([
        'status' => 'error',
        'html'   => $html
    ]);
    exit;
}

// 🌸 Check if email already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    echo json_encode([
        'status' => 'error',
        'html'   => '<div class="alert alert-danger">This email is already registered 💌</div>'
    ]);
    exit;
}

// 💕 Hash the password and insert the new user
$hash = password_hash($password, PASSWORD_DEFAULT);

$insert = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
$insert->execute([$name, $email, $hash]);

echo json_encode([
    'status' => 'success',
    'html'   => '<div class="alert alert-success">Account created successfully! You can log in now ✨</div>'
]);