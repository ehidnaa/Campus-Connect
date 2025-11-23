<?php
// 🌸 API to register the current user for a specific event
// Uses sessions + prepared statements + JSON response ✨

header('Content-Type: application/json');

require_once '../db.php';
session_start();

// 💕 User must be logged in
if (empty($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'html'   => '<div class="alert alert-warning">Please log in to register for events 💻</div>'
    ]);
    exit;
}

$userId  = (int)($_SESSION['user_id']);
$eventId = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;

if ($eventId <= 0) {
    echo json_encode([
        'status' => 'error',
        'html'   => '<div class="alert alert-danger">Invalid event selected 🌧</div>'
    ]);
    exit;
}

try {
    // 🌼 Check if event exists
    $eventStmt = $pdo->prepare("SELECT id, title, date FROM events WHERE id = ?");
    $eventStmt->execute([$eventId]);
    $event = $eventStmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        echo json_encode([
            'status' => 'error',
            'html'   => '<div class="alert alert-danger">This event does not exist anymore 💔</div>'
        ]);
        exit;
    }

    // 🌸 Check if user is already registered
    $checkStmt = $pdo->prepare("SELECT id FROM registrations WHERE user_id = ? AND event_id = ?");
    $checkStmt->execute([$userId, $eventId]);
    if ($checkStmt->fetch()) {
        echo json_encode([
            'status' => 'error',
            'html'   => '<div class="alert alert-info">You are already registered for this event 💕</div>'
        ]);
        exit;
    }

    // 💕 Insert new registration
    $insertStmt = $pdo->prepare("INSERT INTO registrations (user_id, event_id) VALUES (?, ?)");
    $insertStmt->execute([$userId, $eventId]);

    $eventTitle = htmlspecialchars($event['title']);
    $eventDate  = date('d M Y', strtotime($event['date']));

    echo json_encode([
        'status' => 'success',
        'html'   => '<div class="alert alert-success">Successfully registered for <strong>' 
                    . $eventTitle . '</strong> on ' . $eventDate . ' ✨</div>'
    ]);

} catch (Exception $e) {
    // 💔 Something went wrong
    echo json_encode([
        'status' => 'error',
        'html'   => '<div class="alert alert-danger">Could not register right now, please try again later 💔</div>'
    ]);
}