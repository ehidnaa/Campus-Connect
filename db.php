<?php
// 🌸 Hi! This little file connects my PHP project to the MySQL database.
// 🌸 I tried to keep it clean and friendly so future-me understands everything 💖

$host = "localhost";
$dbname = "campus_connect";
$user = "root";
$pass = ""; // WAMP default 🎀

// 🌼 Connecting using PDO
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Database error 🌸: " . $e->getMessage());
}
?>