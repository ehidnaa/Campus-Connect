<?php
// 🌸 Reviews page: lets students leave reviews and see what others said about events
include 'header.php';

// 🌼 Load events for the dropdown (simple PHP query)
require_once 'db.php';

$eventsStmt = $pdo->query("SELECT id, title FROM events ORDER BY date ASC");
$eventsList = $eventsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="mb-3">Event Reviews 💬⭐</h1>
<p class="text-muted mb-4">
    Share your experience about campus events and see what others think 🌸
</p>

<div class="d-flex align-items-center gap-3 mb-4">
    <img src="images/profile-placeholder.png"
         alt="Student profile"
         style="width:64px; height:64px; border-radius:50%; object-fit:cover;">
    <div class="text-muted">
        Real student feedback helps everyone pick the best events ⭐
    </div>
</div>

<div id="reviewMessage"></div>

<?php if (empty($_SESSION['user_id'])): ?>
    <div class="alert alert-warning">
        You need to be logged in to leave a review 💻
    </div>
<?php else: ?>
    <form id="reviewForm" class="card p-4 mb-4 shadow-sm bg-white">
        <div class="mb-3">
            <label class="form-label">Event</label>
            <select name="event_id" class="form-select">
                <option value="">Choose an event…</option>
                <?php foreach ($eventsList as $ev): ?>
                    <option value="<?= (int)$ev['id'] ?>">
                        <?= htmlspecialchars($ev['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Rating</label>
            <select name="rating" class="form-select">
                <option value="">Select rating…</option>
                <option value="5">⭐⭐⭐⭐⭐</option>
                <option value="4">⭐⭐⭐⭐</option>
                <option value="3">⭐⭐⭐</option>
                <option value="2">⭐⭐</option>
                <option value="1">⭐</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Your review</label>
            <textarea name="text" class="form-control" rows="3"
                      placeholder="Write a short, kind and honest review 💕"></textarea>
        </div>

        <button class="btn btn-primary">Submit review ✨</button>
    </form>
<?php endif; ?>

<h2 class="h5 mb-3">Latest reviews</h2>

<div class="mb-3">
    <label class="form-label">Filter by event (optional)</label>
    <select id="reviewsFilterEvent" class="form-select">
        <option value="">All events</option>
        <?php foreach ($eventsList as $ev): ?>
            <option value="<?= (int)$ev['id'] ?>">
                <?= htmlspecialchars($ev['title']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div id="reviewsList">
    <div class="text-muted text-center py-3">Loading reviews… ⏳</div>
</div>

<script>
// 🌸 Load reviews list (optionally filtered by event)
async function loadReviews() {
    const list = document.getElementById('reviewsList');
    const filter = document.getElementById('reviewsFilterEvent');
    const eventId = filter.value;

    let url = 'api/get_reviews.php';
    if (eventId) {
        url += '?event_id=' + encodeURIComponent(eventId);
    }

    try {
        const res  = await fetch(url);
        const data = await res.json();
        list.innerHTML = data.html || '';

    } catch (err) {
        console.error('Error loading reviews', err);
        list.innerHTML = '<div class="alert alert-danger">Could not load reviews 💔</div>';
    }
}

loadReviews();

document.getElementById('reviewsFilterEvent').addEventListener('change', loadReviews);

// 🌼 Handle review form submit (only if form exists, i.e. user is logged in)
const form = document.getElementById('reviewForm');
if (form) {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const msgBox = document.getElementById('reviewMessage');
        const formData = new FormData(form);

        try {
            const res  = await fetch('api/add_review.php', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            msgBox.innerHTML = data.html || '';
            if (data.status === 'success') {
                form.reset();
                loadReviews();
            }
        } catch (err) {
            console.error('Review submit error', err);
            msgBox.innerHTML =
                '<div class="alert alert-danger">Could not submit your review 💔</div>';
        }
    });
}
</script>

<?php
include 'footer.php';
?>