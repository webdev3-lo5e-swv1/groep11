<?php
session_start();
include_once './include/connect.php';
if ($DBerr) { header('location: databaseError.php'); exit; }
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once './include/Reservation.php';
require_once './include/movie.php';

$errors        = [];
$success       = false;
$reservationId = null;
$movieTitle    = '';
$ticketCount   = 0;
$showtimeText  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_id_raw    = trim($_POST['movie_id']      ?? '');
    $showtime_id_raw = trim($_POST['showtime_id']   ?? '');
    $showtime_text   = trim($_POST['showtime_text'] ?? '');
    $ticketsRaw      = trim($_POST['tickets']        ?? '');
    $user_id         = (int) $_SESSION['user_id'];

    // Validatie
    $movie_id    = filter_var($movie_id_raw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $showtime_id = filter_var($showtime_id_raw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $ticketsInt  = filter_var($ticketsRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 10]]);

    if (!$movie_id)   $errors[] = 'Geen film geselecteerd.';
    if (!$showtime_id) $errors[] = 'Geen tijdstip geselecteerd.';
    if ($ticketsInt === false) $errors[] = 'Aantal tickets moet tussen 1 en 10 zijn.';

    if (empty($errors)) {
        // Haal filmtitel op via ID
        $movie = Movie::getById($conn, $movie_id);
        if (!$movie) {
            $errors[] = 'Film niet gevonden.';
        } else {
            $movieTitle   = $movie->getTitle();
            $ticketCount  = $ticketsInt;
            $showtimeText = $showtime_text;

            // Haal start_time op voor weergave
            $stmtS = $conn->prepare("SELECT DATE_FORMAT(start_time,'%H:%i') AS t FROM showtimes WHERE id = :id");
            $stmtS->execute([':id' => $showtime_id]);
            $stRow = $stmtS->fetch(PDO::FETCH_ASSOC);
            if ($stRow) $showtimeText = $stRow['t'];

            $reservation   = new Reservation($user_id, $movie_id, $ticketsInt, $showtime_id, $showtimeText);
            $reservationId = $reservation->save($conn);

            if ($reservationId > 0) {
                $success = true;
            } else {
                $errors[] = 'Reservering opslaan mislukt. Probeer opnieuw.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MBO Cinemas – Bevestiging</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./style/style.css">
</head>
<body>
<?php include_once './include/header.php'; ?>

<main class="confirm-wrap">
<?php if ($success): ?>
    <div class="confirm-box success">
        <h1>✅ Reservering bevestigd!</h1>
        <p><strong>Reserveringsnummer:</strong> #<?= $reservationId ?></p>
        <p><strong>Film:</strong> <?= htmlspecialchars($movieTitle) ?></p>
        <p><strong>Tijdstip:</strong> <?= htmlspecialchars($showtimeText) ?></p>
        <p><strong>Aantal tickets:</strong> <?= $ticketCount ?></p>
        <p style="color:#aaa;font-size:.9rem;">Een bevestiging is opgeslagen in je account.</p>
        <div class="confirm-actions">
            <a href="reservations.php" class="btn-ticket">Mijn reserveringen</a>
            <a href="index.php" class="btn-ticket btn-ticket-secondary">Terug naar home</a>
        </div>
    </div>
    <script>localStorage.removeItem('pendingReservation');</script>
<?php else: ?>
    <div class="confirm-box error">
        <h1>❌ Er ging iets mis</h1>
        <?php foreach ($errors as $e): ?>
            <p style="color:#e63946;"><?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
        <a href="javascript:history.back()" class="btn-ticket">Terug</a>
    </div>
<?php endif; ?>
</main>

<?php include_once './include/footer.php'; ?>
</body>
</html>
