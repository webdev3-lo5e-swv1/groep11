<?php
session_start();
include_once('./include/connect.php');
if ($DBerr == true) {
    header('location: databaseError.php');
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'medewerker') {
    header('Location: index.php');
    exit;
}

require_once('./include/movie.php');

$msg = '';
$errors = [];

// Film aan zaal plannen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plan_movie'])) {
    $movie_id   = (int) ($_POST['movie_id']   ?? 0);
    $hall_id    = (int) ($_POST['hall_id']    ?? 0);
    $start_time = trim($_POST['start_time'] ?? '');

    if (!$movie_id)   $errors[] = 'Kies een film.';
    if (!$hall_id)    $errors[] = 'Kies een zaal.';
    if (!$start_time) $errors[] = 'Kies een begintijd.';

    if (empty($errors)) {
        $stmt = $conn->prepare("
            INSERT INTO showtimes (movie_id, hall_id, start_time)
            VALUES (:movie_id, :hall_id, :start_time)
        ");
        $stmt->execute([
            ':movie_id'   => $movie_id,
            ':hall_id'    => $hall_id,
            ':start_time' => $start_time,
        ]);
        $msg = 'Vertoning ingepland!';
    }
}

// Vertoning verwijderen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_showtime'])) {
    $id = (int) $_POST['delete_showtime'];
    $stmt = $conn->prepare("DELETE FROM showtimes WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $msg = 'Vertoning verwijderd.';
}

$movies   = Movie::getAll($conn);
$hallStmt = $conn->query("
    SELECT h.*, c.name AS cinema_name, c.city
    FROM halls h
    JOIN cinemas c ON h.cinema_id = c.id
    ORDER BY c.city, h.name
");
$halls = $hallStmt ? $hallStmt->fetchAll(PDO::FETCH_ASSOC) : [];

$showStmt = $conn->query("
    SELECT s.*, m.title AS movie_title, h.name AS hall_name, c.city
    FROM showtimes s
    JOIN movies m  ON s.movie_id = m.id
    JOIN halls h   ON s.hall_id  = h.id
    JOIN cinemas c ON h.cinema_id = c.id
    ORDER BY s.start_time DESC
    LIMIT 100
");
$showtimes = $showStmt ? $showStmt->fetchAll(PDO::FETCH_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MBO Cinemas – Zaalplanning</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./style/style.css">
</head>
<body>
<?php include_once('./include/header.php'); ?>

<main class="reservations-wrap">
    <h1>Zaalplanning</h1>

    <?php if ($msg): ?>
        <p class="res-message"><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>
    <?php foreach ($errors as $e): ?>
        <p style="color:red;"><?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>

    <h2>Nieuwe vertoning inplannen</h2>
    <form method="POST" class="employee-form">
        <input type="hidden" name="plan_movie" value="1">
        <label>
            Film *
            <select name="movie_id" required>
                <option value="">– Kies film –</option>
                <?php foreach ($movies as $m): ?>
                <option value="<?= $m->id ?>"><?= htmlspecialchars($m->title) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            Zaal *
            <select name="hall_id" required>
                <option value="">– Kies zaal –</option>
                <?php foreach ($halls as $h): ?>
                <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['cinema_name'] . ' ' . $h['city'] . ' – ' . $h['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            Begintijd *
            <input type="datetime-local" name="start_time" required>
        </label>
        <button type="submit" class="saveMovieBtn">Inplannen</button>
    </form>

    <hr>
    <h2>Geplande vertoningen</h2>

    <?php if (empty($showtimes)): ?>
        <p>Geen vertoningen gepland.</p>
    <?php else: ?>
    <table class="res-table">
        <thead>
            <tr><th>#</th><th>Film</th><th>Zaal</th><th>Locatie</th><th>Begintijd</th><th></th></tr>
        </thead>
        <tbody>
        <?php foreach ($showtimes as $s): ?>
            <tr>
                <td><?= $s['id'] ?></td>
                <td><?= htmlspecialchars($s['movie_title']) ?></td>
                <td><?= htmlspecialchars($s['hall_name']) ?></td>
                <td><?= htmlspecialchars($s['city']) ?></td>
                <td><?= htmlspecialchars($s['start_time']) ?></td>
                <td>
                    <form method="POST" onsubmit="return confirm('Vertoning verwijderen?')">
                        <input type="hidden" name="delete_showtime" value="<?= $s['id'] ?>">
                        <button type="submit" class="btn-small btn-danger">Verwijderen</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</main>

<?php include_once('./include/footer.php'); ?>
</body>
</html>
