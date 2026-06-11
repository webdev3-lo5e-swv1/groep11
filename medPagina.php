<?php
require_once './include/connect.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'medewerker') {
    header('Location: index.php');
    exit;
}

require_once './include/movie.php';

// ── Verwijderen ───────────────────────────────────────────────────────────────
if (isset($_POST['delete_id'])) {
    Movie::delete($conn, (int)$_POST['delete_id']);
    header('Location: medPagina.php?msg=deleted');
    exit;
}

// ── Film bijwerken (edit) ─────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = (int) $_POST['edit_id'];
    Movie::update($conn, $id, [
        'title'       => trim($_POST['title']       ?? ''),
        'age_rating'  => trim($_POST['age_rating']  ?? ''),
        'genre'       => trim($_POST['genre']        ?? ''),
        'duration'    => trim($_POST['duration']     ?? ''),
        'year'        => !empty($_POST['year']) ? (int)$_POST['year'] : null,
        'description' => trim($_POST['description'] ?? ''),
    ]);
    header('Location: medPagina.php?msg=updated');
    exit;
}

// ── Film toevoegen ────────────────────────────────────────────────────────────
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title']) && !isset($_POST['edit_id'])) {
    $title = trim($_POST['title'] ?? '');
    if ($title === '') {
        $errors[] = 'Titel is verplicht.';
        // Sla formulierdata op in sessie zodat het na redirect nog te tonen is
        $_SESSION['form_data'] = $_POST;
    } else {
        $movie = new Movie(
            $title, null,
            './images/movie_posters/' . $title . '.png',
            $title . ' poster',
            trim($_POST['age_rating']  ?? ''),
            trim($_POST['genre']       ?? ''),
            trim($_POST['duration']    ?? ''),
            !empty($_POST['year']) ? (int)$_POST['year'] : null,
            trim($_POST['description'] ?? '')
        );
        $movie->save($conn);
        header('Location: medPagina.php?msg=added');
        exit;
    }
}

// Herstel formulierdata na mislukte POST (PRG-variant)
$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

$msg       = $_GET['msg'] ?? '';
$editId    = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$editMovie = $editId ? Movie::getById($conn, $editId) : null;
$movies    = Movie::getAll($conn);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MBO Cinemas – Medewerker</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./style/style.css">
</head>
<body>
<?php include_once './include/header.php'; ?>

<main class="reservations-wrap">

<?php if ($msg === 'added'):   ?><p class="res-message">✅ Film toegevoegd!</p><?php endif; ?>
<?php if ($msg === 'updated'): ?><p class="res-message">✅ Film bijgewerkt!</p><?php endif; ?>
<?php if ($msg === 'deleted'): ?><p class="res-message">🗑️ Film verwijderd.</p><?php endif; ?>
<?php foreach ($errors as $e): ?><p style="color:#e63946;"><?= htmlspecialchars($e) ?></p><?php endforeach; ?>

<!-- ── Film toevoegen ──────────────────────────────────────────────────── -->
<h1><?= $editMovie ? 'Film wijzigen' : 'Film toevoegen' ?></h1>

<form method="POST" action="medPagina.php" class="employee-form">
    <?php if ($editMovie): ?>
    <input type="hidden" name="edit_id" value="<?= $editMovie->id ?>">
    <?php endif; ?>

    <label>Titel *
        <input type="text" name="title" required
               value="<?= htmlspecialchars($editMovie?->getTitle() ?? ($formData['title'] ?? '')) ?>">
    </label>
    <label>Leeftijdsbeoordeling
        <input type="text" name="age_rating" placeholder="bijv. 12+"
               value="<?= htmlspecialchars($editMovie?->getAgeRating() ?? ($formData['age_rating'] ?? '')) ?>">
    </label>
    <label>Genre
        <input type="text" name="genre" placeholder="bijv. Horror"
               value="<?= htmlspecialchars($editMovie?->getGenre() ?? ($formData['genre'] ?? '')) ?>">
    </label>
    <label>Duur
        <input type="text" name="duration" placeholder="bijv. 1u 44m"
               value="<?= htmlspecialchars($editMovie?->getDuration() ?? ($formData['duration'] ?? '')) ?>">
    </label>
    <label>Jaar
        <input type="number" name="year" min="1888" max="2100" placeholder="2025"
               value="<?= htmlspecialchars((string)($editMovie?->getYear() ?? ($formData['year'] ?? ''))) ?>">
    </label>
    <label>Beschrijving
        <textarea name="description" rows="4"><?= htmlspecialchars($editMovie?->getDescription() ?? ($formData['description'] ?? '')) ?></textarea>
    </label>

    <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
        <button type="submit" class="saveMovieBtn"><?= $editMovie ? 'Opslaan' : 'Film toevoegen' ?></button>
        <?php if ($editMovie): ?>
        <a href="medPagina.php" class="btn-small" style="padding:.55rem 1rem;text-decoration:none;">Annuleren</a>
        <?php endif; ?>
    </div>
</form>

<hr style="margin:2rem 0;border-color:#1c2f68;">

<!-- ── Films beheren ──────────────────────────────────────────────────── -->
<h2>Films beheren</h2>
<input type="text" id="movieSearch" placeholder="Zoek film...">

<ul id="movieList">
<?php foreach ($movies as $movie): ?>
<li class="movieItem">
    <div class="movieInfo">
        <span class="movieId">#<?= $movie->id ?></span>
        <span class="movieTitle"><?= htmlspecialchars($movie->getTitle()) ?></span>
        <span style="font-size:.8rem;color:#aaa;"><?= htmlspecialchars($movie->getGenre() ?? '') ?></span>
    </div>
    <div style="display:flex;gap:.5rem;">
        <a href="medPagina.php?edit=<?= $movie->id ?>" class="btn-small">Wijzigen</a>
        <form method="POST" onsubmit="return confirmDelete('<?= htmlspecialchars(addslashes($movie->getTitle())) ?>')">
            <input type="hidden" name="delete_id" value="<?= $movie->id ?>">
            <button type="submit" class="btn-small btn-danger">Verwijderen</button>
        </form>
    </div>
</li>
<?php endforeach; ?>
</ul>

</main>

<?php include_once './include/footer.php'; ?>
<script type="module" src="./src/app.js"></script>
</body>
</html>
