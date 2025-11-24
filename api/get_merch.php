<?php
// 🌸 Merch API for Campus Connect
// Returns a cute grid of merch items as HTML inside JSON 🛍️

header('Content-Type: application/json');

require_once '../db.php';

try {
    $stmt = $pdo->query("SELECT id, name, price, image FROM merch ORDER BY id ASC");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$items) {
        echo json_encode([
            'status' => 'success',
            'html'   => '<div class="alert alert-info">No merch available yet… maybe soon 💚</div>'
        ]);
        exit;
    }

    $html = '<div class="row g-3">';
    foreach ($items as $item) {
        $html .= '<div class="col-md-4">';
        $html .= '  <div class="card h-100 shadow-sm">';
        if (!empty($item['image'])) {
            $html .= '    <img src="images/' . htmlspecialchars($item['image']) . '" class="card-img-top" alt="Merch image">';
        }
        $html .= '    <div class="card-body d-flex flex-column">';
        $html .= '      <h5 class="card-title">' . htmlspecialchars($item['name']) . '</h5>';
        $html .= '      <p class="card-text mb-2">€' . number_format($item['price'], 2) . '</p>';
        $html .= '      <button class="btn btn-sm btn-outline-primary mt-auto merch-add-btn" ';
        $html .= '              data-merch-id="' . (int)$item['id'] . '">';
        $html .= '        Add to cart ✨';
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
    echo json_encode([
        'status' => 'error',
        'html'   => '<div class="alert alert-danger">Could not load merch right now 💔</div>'
    ]);
}