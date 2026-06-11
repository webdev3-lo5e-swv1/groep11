<?php
session_start();
include_once('./include/connect.php');
if ($DBerr == true) {
    header('location: databaseError.php');
    exit;
}
require_once('./include/movie.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$movie = htmlspecialchars($_GET['movie'] ?? '');
$time  = htmlspecialchars($_GET['time']  ?? '');
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
<?php include_once('./include/header.php'); ?>

<main class="reservation-wrap">
    <h1>Reservering aanmaken</h1>

    <div id="reserveForm">
        <p><strong>Film:</strong> <span id="displayMovie"><?= $movie ?></span></p>
        <p><strong>Tijdstip:</strong> <span id="displayTime"><?= $time ?: '(nog niet gekozen)' ?></span></p>

        <form action="saveReservation.php" method="post" id="reservationForm" novalidate>
            <input type="hidden" name="movie" id="movieInput" value="<?= $movie ?>">
            <input type="hidden" name="time"  id="timeInput"  value="<?= $time ?>">

            <label>
                Naam *
                <input type="text" name="naam" id="naam" placeholder="Jouw naam" required>
                <span class="field-error" id="err-naam"></span>
            </label>
            <label>
                E-mail *
                <input type="email" name="email" id="email" placeholder="jouw@email.nl" required>
                <span class="field-error" id="err-email"></span>
            </label>
            <label>
                Aantal tickets *
                <input type="number" name="tickets" id="tickets" min="1" max="10" value="1" required>
                <span class="field-error" id="err-tickets"></span>
            </label>

            <p class="total-price" id="totalPrice">Totaal: €10,00</p>

            <button type="submit" class="btn-ticket">Bevestigen</button>
        </form>
    </div>
</main>

<?php include_once('./include/footer.php'); ?>
<script>
// ── Local Storage: herstel eerder ingevulde gegevens ──────────────────────────
const STORAGE_KEY = 'pendingReservation';

(function restoreForm() {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (!saved) return;
    try {
        const data = JSON.parse(saved);
        if (data.naam)    document.getElementById('naam').value    = data.naam;
        if (data.email)   document.getElementById('email').value   = data.email;
        if (data.tickets) document.getElementById('tickets').value = data.tickets;
        if (data.movie && !document.getElementById('movieInput').value) {
            document.getElementById('movieInput').value  = data.movie;
            document.getElementById('displayMovie').textContent = data.movie;
        }
        if (data.time && !document.getElementById('timeInput').value) {
            document.getElementById('timeInput').value  = data.time;
            document.getElementById('displayTime').textContent = data.time;
        }
    } catch(e) {}
})();

// ── Sla voortgang op in Local Storage bij elke wijziging ─────────────────────
['naam','email','tickets'].forEach(id => {
    document.getElementById(id).addEventListener('input', saveProgress);
});
function saveProgress() {
    const data = {
        naam:    document.getElementById('naam').value,
        email:   document.getElementById('email').value,
        tickets: document.getElementById('tickets').value,
        movie:   document.getElementById('movieInput').value,
        time:    document.getElementById('timeInput').value,
    };
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
}

// ── Prijs live berekenen ──────────────────────────────────────────────────────
const PRICE_PER_TICKET = 10;
document.getElementById('tickets').addEventListener('input', function() {
    const n = Math.max(1, parseInt(this.value) || 1);
    document.getElementById('totalPrice').textContent =
        'Totaal: €' + (n * PRICE_PER_TICKET).toFixed(2).replace('.', ',');
});

// ── Frontend validatie ────────────────────────────────────────────────────────
document.getElementById('reservationForm').addEventListener('submit', function(e) {
    let valid = true;
    const naam    = document.getElementById('naam');
    const email   = document.getElementById('email');
    const tickets = document.getElementById('tickets');

    // Reset errors
    document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
    [naam, email, tickets].forEach(el => el.classList.remove('input-error'));

    if (!naam.value.trim()) {
        document.getElementById('err-naam').textContent = 'Naam is verplicht.';
        naam.classList.add('input-error');
        valid = false;
    }

    const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRe.test(email.value.trim())) {
        document.getElementById('err-email').textContent = 'Vul een geldig e-mailadres in.';
        email.classList.add('input-error');
        valid = false;
    }

    const t = parseInt(tickets.value);
    if (isNaN(t) || t < 1 || t > 10) {
        document.getElementById('err-tickets').textContent = 'Kies 1 tot 10 tickets.';
        tickets.classList.add('input-error');
        valid = false;
    }

    if (!valid) e.preventDefault();
});
</script>
</body>
</html>
