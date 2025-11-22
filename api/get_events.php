<?php
// 🌸 Events API for Campus Connect
// Returns a cute HTML list of events inside JSON so JS can render it ✨

header('Content-Type: application/json');

require_once '../db.php';
session_start();

try {
    // 🌼 Grab all events + attendee count + average rating (just for fun)
    $sql = "
        SELECT 
            e.id,
            e.title,
            e.description,
            e.date,
            e.location,
            COUNT(r.id) AS attendee_count,
            ROUND(AVG(rv.rating), 1) AS avg_rating
        FROM events e
        LEFT JOIN registrations r ON r.event_id = e.id
        LEFT JOIN reviews rv ON rv.event_id = e.id
        GROUP BY e.id
        ORDER BY e.date ASC
    ";

    $stmt = $pdo->query($sql);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$events) {
        echo json_encode([
            'status' => 'success',
            'html'   => '<div class="alert alert-info">No events yet… maybe soon ✨</div>'
        ]);
        exit;
    }

    // 💕 Build HTML cards with Bootstrap
    $html = '<div class="row g-3">';
    foreach ($events as $event) {
        $html .= '<div class="col-md-6">';
        $html .= '  <div class="card shadow-sm h-100">';
        $html .= '    <div class="card-body">';
        $html .= '      <h5 class="card-title">' . htmlspecialchars($event['title']) . '</h5>';
        $html .= '      <p class="card-text small text-muted mb-1">';
        $html .=            date('d M Y', strtotime($event['date'])) . ' · ';
        $html .=            htmlspecialchars($event['location']);
        $html .= '      </p>';

        if (!empty($event['description'])) {
            $html .= '  <p class="card-text">' . nl2br(htmlspecialchars($event['description'])) . '</p>';
        }

        $html .= '      <p class="card-text small mb-2">';
        $html .= '        Attendees: ' . (int)$event['attendee_count'] . ' 🧸';
        if ($event['avg_rating'] !== null) {
            $html .= ' · Rating: ' . htmlspecialchars($event['avg_rating']) . ' ⭐';
        }
        $html .= '      </p>';

        $html .= '      <button class="btn btn-sm btn-outline-primary register-btn" ';
        $html .= '              data-event-id="' . (int)$event['id'] . '">';
        $html .= '        Register ✨';
        $html .= '      </button>';

        $html .= '    </div>';
        $html .= '  </div>';
        $html .= '</div>';
    }
    $html .= '</div>';

    echo json_encode([
        'status' => 'success',
        'html'   => $html
    ]);

} catch (Exception $e) {
    // 💔 If something goes wrong, send a friendly error message
    echo json_encode([
        'status' => 'error',
        'html'   => '<div class="alert alert-danger">Could not load events right now 💔</div>'
    ]);
}