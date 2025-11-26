<?php
// 🌸 Add Review API
// Lets a logged-in student leave a rating + comment for an event 💬✨

header('Content-Type: application/json');

require_once '../db.php';
session_start();

// 💕 Must be logged in
if (empty($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'html'   => '<div class="alert alert-warning">Please log in to leave a review 💻</div>'
    ]);
    exit;
}

$userId  = (int)$_SESSION['user_id'];
$eventId = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
$rating  = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$text    = trim($_POST['text'] ?? '');

$errors = [];

// 🌼 Simple validation
if ($eventId <= 0) {
    $errors[] = 'Please select an event 🌸';
}
if ($rating < 1 || $rating > 5) {
    $errors[] = 'Rating must be between 1 and 5 ⭐';
}
if ($text === '') {
    $errors[] = 'Please write a short comment 💬';
} elseif (mb_strlen($text) > 1000) {
    $errors[] = 'Review is too long… keep it under 1000 characters 💕';
}

if (!empty($errors)) {
    $html = '<div class="alert alert-danger"><ul class="mb-0">';
    foreach ($errors as $e) {
        $html .= '<li>' . htmlspecialchars($e) . '</li>';
    }
    $html .= '</ul></div>';

    echo json_encode([
        'status' => 'error',
        'html'   => $html
    ]);
    exit;
}

try {
    // 🌼 Check event existence
    $eventStmt = $pdo->prepare("SELECT id, title FROM events WHERE id = ?");
    $eventStmt->execute([$eventId]);
    $event = $eventStmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        echo json_encode([
            'status' => 'error',
            'html'   => '<div class="alert alert-danger">This event does not exist anymore 💔</div>'
        ]);
        exit;
    }

    // Optional: prevent multiple reviews by same user for same event
    $checkStmt = $pdo->prepare("SELECT id FROM reviews WHERE user_id = ? AND event_id = ?");
    $checkStmt->execute([$userId, $eventId]);
    if ($checkStmt->fetch()) {
        echo json_encode([
            'status' => 'error',
            'html'   => '<div class="alert alert-info">You already reviewed this event 💕</div>'
        ]);
        exit;
    }

    // 💕 Insert review
    $insert = $pdo->prepare(
        "INSERT INTO reviews (user_id, event_id, rating, text) VALUES (?, ?, ?, ?)"
    );
    $insert->execute([$userId, $eventId, $rating, $text]);

    echo json_encode([
        'status' => 'success',
        'html'   => '<div class="alert alert-success">Thank you for your review! ✨</div>'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'html'   => '<div class="alert alert-danger">Could not save your review right now 💔</div>'
    ]);
}