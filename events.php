<?php
// 🌸 Events page: shows all upcoming campus events using fetch() + JSON
include 'header.php';
?>

<h1 class="mb-3">Upcoming Events 🌼</h1>
<p class="text-muted mb-4">
    Here are some things happening around campus. Click "Register" to join ✨
</p>

<img src="images/event-placeholder.jpg"
     alt="Campus events"
     class="img-fluid rounded mb-4 w-100"
     style="max-height:260px; object-fit:cover;">

<div id="eventsMessage"></div>

<div id="eventsList">
    <!-- 🌸 Events will be loaded here via JavaScript -->
    <div class="text-center text-muted py-4">Loading events… ⏳</div>
</div>

<script>
// 🌸 Load events from the API when the page is ready
async function loadEvents() {
    const list = document.getElementById('eventsList');
    const msg  = document.getElementById('eventsMessage');

    try {
        const res  = await fetch('api/get_events.php');
        const data = await res.json();

        list.innerHTML = data.html || '';
        if (data.status !== 'success') {
            msg.innerHTML = '<div class="alert alert-warning">Could not fetch events 🌧</div>';
        }

    } catch (err) {
        console.error('Error loading events', err);
        list.innerHTML = '<div class="alert alert-danger">Server error while loading events 💔</div>';
    }
}

loadEvents();

// 🌼 Event delegation for "Register" buttons
document.getElementById('eventsList').addEventListener('click', async (e) => {
    if (!e.target.classList.contains('register-btn')) return;

    const eventId = e.target.getAttribute('data-event-id');
    const msg = document.getElementById('eventsMessage');

    const formData = new FormData();
    formData.append('event_id', eventId);

    try {
        const res  = await fetch('api/register_event.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        msg.innerHTML = data.html || '';

        // 🌸 Optionally reload events to update attendee counts
        if (data.status === 'success') {
            loadEvents();
        }

    } catch (err) {
        console.error('Register error', err);
        msg.innerHTML = '<div class="alert alert-danger">Could not register for the event 💔</div>';
    }
});
</script>

<?php
include 'footer.php';
?>