<?php
require_once('./include/connect.php');

session_start();

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'medewerker'
) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MBO Cinemas</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./style/style.css">
</head>
<body>
<?php
include_once('./include/header.php')
?>
<?php
require_once './include/Movie.php';

$success = false;
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title      = trim($_POST['title']      ?? '');
    $posterPath = './images/movie_posters/' . $title . '.png';
    $posterAlt  = $title . ' poster';
    $ageRating  = trim($_POST['age_rating']  ?? '');
    $genre      = trim($_POST['genre']       ?? '');
    $duration   = trim($_POST['duration']    ?? '');
    $year       = !empty($_POST['year']) ? (int)$_POST['year'] : null;
    $synopsis   = trim($_POST['synopsis']   ?? '');

    // Basic validation
    if ($title === '') {
        $errors[] = 'Title is required.';
    }

    if (empty($errors)) {
        $movie = new Movie($title, $posterPath, $posterAlt, $ageRating, $genre, $duration, $year, $synopsis);
        $id    = $movie->save($conn);

        if ($id !== false) {
            $success = true;
        } else {
            $errors[] = 'Failed to save movie. Please try again.';
        }
    }
}
?>
    <h1>Add a New Movie</h1>

    <?php if ($success): ?>
        <p style="color:green;">Movie added successfully!</p>
    <?php endif; ?>

    <?php foreach ($errors as $e): ?>
        <p style="color:red;"><?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>

    <form method="POST" action="medPagina.php">
        <label>Title *
            <input type="text" name="title" required 
            value="<?= $success ? '' : htmlspecialchars($_POST['title'] ?? '') ?>">
        </label>
            <br>
        <label>Age rating
            <input type="text" name="age_rating" placeholder="rating"
                   value="<?= $success ? '' : htmlspecialchars($_POST['age_rating'] ?? '') ?>">
        </label>
            <br>
        <label>Genre
            <input type="text" name="genre" placeholder="genre"
                   value="<?= $success ? '' : htmlspecialchars($_POST['genre'] ?? '') ?>">
        </label>
            <br>
        <label>Duration
            <input type="text" name="duration" placeholder="..h ..m"
                   value="<?= $success ? '' : htmlspecialchars($_POST['duration'] ?? '') ?>">
        </label>
            <br>
        <label>Year
            <input type="number" name="year" min="1888" max="2100" placeholder="...."
                   value="<?= $success ? '' : htmlspecialchars($_POST['year'] ?? '') ?>">
        </label>
            <br>
        <label>description
            <textarea name="synopsis" rows="4"><?= $success ? '' : htmlspecialchars($_POST['synopsis'] ?? '') ?></textarea>
        </label>
<br>
        <button type="submit">Add Movie</button>
    </form>
<?php
include_once('./include/footer.php')
?>
</body>
</html>