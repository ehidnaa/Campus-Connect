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
        $id    = (int)($item['id'] ?? 0);
        $name  = (string)($item['name'] ?? '');
        $price = (float)($item['price'] ?? 0);
        $img   = (string)($item['image'] ?? '');

        // ✅ If DB image is empty, show placeholder
        $imageFile = $img !== '' ? $img : 'merch-placeholder.jpg';

        $html .= '<div class="col-md-4">';
        $html .= '  <div class="card h-100 shadow-sm">';

        // ✅ Emoji instead of image (no broken paths ever)
        $html .= '    <div class="text-center py-4 fs-1">👕</div>';

        $html .= '    <div class="card-body d-flex flex-column">';
        $html .= '      <h5 class="card-title">' . htmlspecialchars($name) . '</h5>';
        $html .= '      <p class="card-text mb-2">€' . number_format($price, 2) . '</p>';
        $html .= '      <button class="btn btn-sm btn-outline-primary mt-auto merch-add-btn" data-merch-id="' . $id . '">';
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
