<?php
session_start();
include_once('./include/connect.php');
if ($DBerr == true) {
    header('location: databaseError.php');
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once('./include/Reservation.php');
require_once('./include/movie.php');

$errors        = [];
$success       = false;
$reservationId = null;
$movieTitle    = '';
$ticketCount   = 0;
$showtime      = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_name = trim($_POST['movie']   ?? '');
    $time       = trim($_POST['time']    ?? '');
    $ticketsRaw = trim($_POST['tickets'] ?? '');
    $user_id    = (int) $_SESSION['user_id'];

    // Validatie
    if ($movie_name === '') {
        $errors[] = 'Filmtitel ontbreekt.';
    }
    $ticketsInt = filter_var($ticketsRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 10]]);
    if ($ticketsInt === false) {
        $errors[] = 'Aantal tickets moet een getal tussen 1 en 10 zijn.';
    }

    if (empty($errors)) {
        // Zoek film op naam (case-insensitive)
        $stmt = $conn->prepare("SELECT id, title FROM movies WHERE LOWER(title) = LOWER(:title) LIMIT 1");
        $stmt->execute([':title' => $movie_name]);
        $movie = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fallback: LIKE
        if (!$movie) {
            $stmt2 = $conn->prepare("SELECT id, title FROM movies WHERE title LIKE :title LIMIT 1");
            $stmt2->execute([':title' => '%' . $movie_name . '%']);
            $movie = $stmt2->fetch(PDO::FETCH_ASSOC);
        }

        if (!$movie) {
            $errors[] = 'Film niet gevonden in de database.';
        } else {
            $movie_id    = (int) $movie['id'];
            $movieTitle  = $movie['title'];
            $ticketCount = $ticketsInt;
            $showtime    = $time;

            // Probeer een showtime_id te vinden (optioneel)
            $showtime_id = null;
            if (!empty($time)) {
                $stmtS = $conn->prepare("
                    SELECT id FROM showtimes
                    WHERE movie_id = :mid
                    ORDER BY ABS(UNIX_TIMESTAMP(start_time) - UNIX_TIMESTAMP(NOW()))
                    LIMIT 1
                ");
                $stmtS->execute([':mid' => $movie_id]);
                $st = $stmtS->fetch(PDO::FETCH_ASSOC);
                if ($st) $showtime_id = (int) $st['id'];
            }

            // Sla het tijdstip altijd op als tekst, ongeacht showtime_id
            $showtime_text = $time !== '' ? $time : null;

            $reservation   = new Reservation($user_id, $movie_id, $ticketsInt, $showtime_id, $showtime_text);
            $reservationId = $reservation->save($conn);

            if ($reservationId !== false && $reservationId > 0) {
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
<?php include_once('./include/header.php'); ?>

<main class="confirm-wrap">
<?php if ($success): ?>
    <div class="confirm-box success">
        <h1>✅ Reservering bevestigd!</h1>
        <p><strong>Reserveringsnummer:</strong> #<?= $reservationId ?></p>
        <p><strong>Film:</strong> <?= htmlspecialchars($movieTitle) ?></p>
        <p><strong>Aantal tickets:</strong> <?= $ticketCount ?></p>
        <?php if ($showtime): ?>
        <p><strong>Tijdstip:</strong> <?= htmlspecialchars($showtime) ?></p>
        <?php endif; ?>
        <p>Een bevestiging is opgeslagen in je account.</p>
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

<?php include_once('./include/footer.php'); ?>
</body>
</html>
