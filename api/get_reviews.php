<?php
// 🌸 Get Reviews API
// Returns a list of reviews for a given event (or all events) as pretty HTML 💬⭐

header('Content-Type: application/json');

require_once '../db.php';
session_start();

$eventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

try {
    if ($eventId > 0) {
        // 🌼 Reviews for one specific event
        $stmt = $pdo->prepare("
            SELECT 
                r.id,
                r.rating,
                r.text,
                r.created_at,
                u.name AS user_name,
                e.title AS event_title
            FROM reviews r
            JOIN users u ON u.id = r.user_id
            JOIN events e ON e.id = r.event_id
            WHERE r.event_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$eventId]);
    } else {
        // 🌼 All recent reviews
        $stmt = $pdo->query("
            SELECT 
                r.id,
                r.rating,
                r.text,
                r.created_at,
                u.name AS user_name,
                e.title AS event_title
            FROM reviews r
            JOIN users u ON u.id = r.user_id
            JOIN events e ON e.id = r.event_id
            ORDER BY r.created_at DESC
            LIMIT 20
        ");
    }

    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$reviews) {
        echo json_encode([
            'status' => 'success',
            'html'   => '<div class="alert alert-info mb-0">No reviews yet… be the first one ✨</div>'
        ]);
        exit;
    }

    // 💕 Build HTML list
    $html = '<div class="list-group">';
    foreach ($reviews as $rev) {
        $stars = str_repeat('⭐', (int)$rev['rating']);
        $date  = date('d M Y H:i', strtotime($rev['created_at']));

        $html .= '<div class="list-group-item">';
        $html .= '  <div class="d-flex justify-content-between">';
        $html .= '    <strong>' . htmlspecialchars($rev['event_title']) . '</strong>';
        $html .= '    <span class="text-muted small">' . $date . '</span>';
        $html .= '  </div>';
        $html .= '  <div class="small text-muted mb-1">';
        $html .=        htmlspecialchars($rev['user_name']) . ' · ' . $stars;
        $html .= '  </div>';
        $html .= '  <p class="mb-0">' . nl2br(htmlspecialchars($rev['text'])) . '</p>';
        $html .= '</div>';
    }
    $html .= '</div>';

    echo json_encode([
        'status' => 'success',
        'html'   => $html
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'html'   => '<div class="alert alert-danger mb-0">Could not load reviews right now 💔</div>'
    ]);
}