<?php
session_start();
include_once './include/connect.php';
if ($DBerr) { header('location: databaseError.php'); exit; }
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once './include/movie.php';

// Haal alle films op voor dropdown
$allMovies = Movie::getAll($conn);

// Voorgeselecteerde waarden via URL
$selMovieTitle   = trim($_GET['movie']       ?? '');
$selTime         = trim($_GET['time']        ?? '');
$selMovieId      = isset($_GET['movie_id'])    ? (int)$_GET['movie_id']    : null;
$selShowtimeId   = isset($_GET['showtime_id']) ? (int)$_GET['showtime_id'] : null;

// Als alleen de titel meekwam, zoek het ID erbij
if ($selMovieTitle && !$selMovieId) {
    foreach ($allMovies as $m) {
        if (strtolower($m->getTitle()) === strtolower($selMovieTitle)) {
            $selMovieId = $m->getId();
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MBO Cinemas – Reservering</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./style/style.css">
</head>
<body>
<?php include_once './include/header.php'; ?>

<main class="reservation-wrap">
    <h1>Reservering aanmaken</h1>

    <form action="saveReservation.php" method="post" id="reservationForm" novalidate>

        <!-- ── Film kiezen ──────────────────────────────────────────────── -->
        <label>Film *
            <select id="movieSelect" name="movie_id" required>
                <option value="">– Kies een film –</option>
                <?php foreach ($allMovies as $m): ?>
                <option value="<?= $m->getId() ?>"
                        data-title="<?= htmlspecialchars($m->getTitle()) ?>"
                        <?= $selMovieId === $m->getId() ? 'selected' : '' ?>>
                    <?= htmlspecialchars($m->getTitle()) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <span class="field-error" id="err-movie"></span>
        </label>

        <!-- ── Tijdstip kiezen ──────────────────────────────────────────── -->
        <label>Tijdstip *
            <div id="showtimeContainer" class="showtime-picker">
                <?php if ($selMovieId): ?>
                    <p id="showtimeLoading" style="color:#aaa;font-size:.9rem;">Tijden laden...</p>
                <?php else: ?>
                    <p id="showtimeHint" style="color:#aaa;font-size:.9rem;">Kies eerst een film.</p>
                <?php endif; ?>
            </div>
            <input type="hidden" name="showtime_id" id="showtimeIdInput" value="<?= $selShowtimeId ?? '' ?>">
            <input type="hidden" name="showtime_text" id="showtimeTextInput" value="<?= htmlspecialchars($selTime) ?>">
            <input type="hidden" name="movie_title" id="movieTitleInput" value="<?= htmlspecialchars($selMovieTitle) ?>">
            <span class="field-error" id="err-time"></span>
        </label>

        <!-- ── Persoonsgegevens ─────────────────────────────────────────── -->
        <label>Naam *
            <input type="text" name="naam" id="naam" placeholder="Jouw naam" required autocomplete="name">
            <span class="field-error" id="err-naam"></span>
        </label>
        <label>E-mail *
            <input type="email" name="email" id="email" placeholder="jouw@email.nl" required autocomplete="email">
            <span class="field-error" id="err-email"></span>
        </label>
        <label>Aantal tickets *
            <input type="number" name="tickets" id="tickets" min="1" max="10" value="1" required>
            <span class="field-error" id="err-tickets"></span>
        </label>

        <p class="total-price" id="totalPrice">Totaal: €10,00</p>

        <button type="submit" class="btn-ticket">Bevestigen</button>
    </form>
</main>

<?php include_once './include/footer.php'; ?>

<script type="module">
import { ReservationForm } from './src/ReservationForm.js';
new ReservationForm('reservationForm');

const PRICE = 10;
const STORAGE_KEY = 'pendingReservation';

const movieSelect   = document.getElementById('movieSelect');
const stContainer   = document.getElementById('showtimeContainer');
const stIdInput     = document.getElementById('showtimeIdInput');
const stTextInput   = document.getElementById('showtimeTextInput');
const movieTitleIn  = document.getElementById('movieTitleInput');

let selectedShowtimeId   = <?= $selShowtimeId ? (int)$selShowtimeId : 'null' ?>;
let selectedShowtimeText = <?= $selTime ? json_encode($selTime) : '""' ?>;

// ── Laad tijden bij filmkeuze ─────────────────────────────────────────────────
async function loadShowtimes(movieId, preselect) {
    stContainer.innerHTML = '<p style="color:#aaa;font-size:.9rem;">Tijden laden...</p>';
    stIdInput.value   = '';
    stTextInput.value = '';
    selectedShowtimeId   = null;
    selectedShowtimeText = '';

    try {
        const res  = await fetch(`api/showtimes.php?movie_id=${movieId}`);
        const data = await res.json();
        renderShowtimes(data.showtimes ?? [], preselect);
    } catch(e) {
        stContainer.innerHTML = '<p style="color:#e63946;">Kon tijden niet laden.</p>';
    }
}

function renderShowtimes(showtimes, preselect) {
    if (!showtimes.length) {
        stContainer.innerHTML = '<p style="color:#aaa;font-size:.9rem;">Geen vertoningen gepland.</p>';
        return;
    }

    // Groepeer op stad
    const byCity = {};
    showtimes.forEach(s => {
        if (!byCity[s.city]) byCity[s.city] = [];
        byCity[s.city].push(s);
    });

    let html = '';
    Object.entries(byCity).forEach(([city, times]) => {
        html += `<p class="showtime-city">${city}</p><div class="showtime-times">`;
        times.forEach(s => {
            const active = (preselect && String(s.id) === String(preselect)) ? ' active' : '';
            html += `<button type="button"
                              class="time-btn showtime-opt${active}"
                              data-id="${s.id}"
                              data-time="${s.start_time}"
                              data-display="${s.time}"
                              title="${s.cinema} · ${s.hall} · ${s.seats} stoelen">
                        ${s.time}
                        <span class="time-hall">${s.hall}</span>
                     </button>`;
        });
        html += '</div>';
    });

    stContainer.innerHTML = html;

    // Klik-handler
    stContainer.querySelectorAll('.showtime-opt').forEach(btn => {
        btn.addEventListener('click', () => {
            stContainer.querySelectorAll('.showtime-opt').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedShowtimeId   = btn.dataset.id;
            selectedShowtimeText = btn.dataset.display;
            stIdInput.value   = btn.dataset.id;
            stTextInput.value = btn.dataset.display;
            saveProgress();
        });
    });

    // Herstel selectie als al gekozen
    if (preselect) {
        const pre = stContainer.querySelector(`[data-id="${preselect}"]`);
        if (pre) {
            selectedShowtimeId   = preselect;
            selectedShowtimeText = pre.dataset.display;
            stIdInput.value   = preselect;
            stTextInput.value = pre.dataset.display;
        }
    }
}

// ── Film dropdown change ──────────────────────────────────────────────────────
movieSelect.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    movieTitleIn.value = opt.dataset.title ?? '';
    if (this.value) {
        loadShowtimes(this.value, null);
        saveProgress();
    } else {
        stContainer.innerHTML = '<p id="showtimeHint" style="color:#aaa;font-size:.9rem;">Kies eerst een film.</p>';
    }
});

// ── Auto-laad als film al voorgeselecteerd is ────────────────────────────────
const preMovieId = <?= $selMovieId ? (int)$selMovieId : 'null' ?>;
const preShowId  = <?= $selShowtimeId ? (int)$selShowtimeId : 'null' ?>;
if (preMovieId) {
    loadShowtimes(preMovieId, preShowId);
}

// ── Local Storage herstel ─────────────────────────────────────────────────────
(function restoreForm() {
    try {
        const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
        if (saved.naam)    document.getElementById('naam').value    = saved.naam;
        if (saved.email)   document.getElementById('email').value   = saved.email;
        if (saved.tickets) document.getElementById('tickets').value = saved.tickets;
        updatePrice();
    } catch(e) {}
})();

// ── Opslaan in Local Storage ─────────────────────────────────────────────────
function saveProgress() {
    const existing = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
    existing.naam       = document.getElementById('naam')?.value    ?? '';
    existing.email      = document.getElementById('email')?.value   ?? '';
    existing.tickets    = document.getElementById('tickets')?.value ?? '1';
    existing.movieId    = movieSelect.value;
    existing.showtimeId = selectedShowtimeId;
    localStorage.setItem(STORAGE_KEY, JSON.stringify(existing));
}
['naam','email','tickets'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', saveProgress);
});

// ── Prijs ─────────────────────────────────────────────────────────────────────
function updatePrice() {
    const n = Math.max(1, parseInt(document.getElementById('tickets')?.value) || 1);
    const el = document.getElementById('totalPrice');
    if (el) el.textContent = 'Totaal: €' + (n * PRICE).toFixed(2).replace('.', ',');
}
document.getElementById('tickets')?.addEventListener('input', updatePrice);

// ── Validatie ─────────────────────────────────────────────────────────────────
document.getElementById('reservationForm').addEventListener('submit', function(e) {
    let valid = true;
    document.querySelectorAll('.field-error').forEach(el => el.textContent = '');

    if (!movieSelect.value) {
        document.getElementById('err-movie').textContent = 'Kies een film.';
        valid = false;
    }
    if (!stIdInput.value) {
        document.getElementById('err-time').textContent = 'Kies een tijdstip.';
        valid = false;
    }

    const naam = document.getElementById('naam');
    if (!naam.value.trim()) {
        document.getElementById('err-naam').textContent = 'Naam is verplicht.';
        naam.classList.add('input-error');
        valid = false;
    }
    const email = document.getElementById('email');
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
        document.getElementById('err-email').textContent = 'Vul een geldig e-mailadres in.';
        email.classList.add('input-error');
        valid = false;
    }
    const t = parseInt(document.getElementById('tickets').value);
    if (isNaN(t) || t < 1 || t > 10) {
        document.getElementById('err-tickets').textContent = 'Kies 1 tot 10 tickets.';
        valid = false;
    }

    if (!valid) e.preventDefault();
});
</script>
</body>
</html>
