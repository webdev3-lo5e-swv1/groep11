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
?><!DOCTYPE html>
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
require_once './include/movie.php';

$success = false;
$errors  = [];
if(isset($_POST['delete_id']))
{
    Movie::delete(
        $conn,
        (int)$_POST['delete_id']
    );

    header("Location: medPagina.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title      = trim($_POST['title']      ?? '');
    $posterPath = './images/movie_posters/' . $title . '.png';
    $posterAlt  = $title . ' poster';
    $ageRating  = trim($_POST['age_rating']  ?? '');
    $genre      = trim($_POST['genre']       ?? '');
    $duration   = trim($_POST['duration']    ?? '');
    $year       = !empty($_POST['year']) ? (int)$_POST['year'] : null;
    $description   = trim($_POST['description']   ?? '');

    // Basic validation
    if ($title === '') {
        $errors[] = 'Title is required.';
    };

    if (empty($errors)) {
        $movie = new Movie($title, null, $posterPath, $posterAlt, $ageRating, $genre, $duration, $year, $description);
        $id    = $movie->save($conn);

        if ($id !== false) {
            header('Location: medPagina.php?success=1');
            exit;
        } else {
            $errors[] = 'Failed to save movie. Please try again.';
        }
    }
}
$success = isset($_GET['success']) && $_GET['success'] == 1;
?>
    <h1>Add a New Movie</h1>

    <?php if ($success): ?>
        <p style="color:green;">Movie added successfully!</p>
    <?php endif; ?>

    <?php foreach ($errors as $e): ?>
        <p style="color:red;"><?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>

<form method="POST" action="medPagina.php" class="employee-form">
    <label>
        Title *
        <input
            type="text"
            name="title"
            required
            value="<?= $success ? '' : htmlspecialchars($_POST['title'] ?? '') ?>"
        >
    </label>
    <label>
        Age rating
        <input
            type="text"
            name="age_rating"
            placeholder="rating"
            value="<?= $success ? '' : htmlspecialchars($_POST['age_rating'] ?? '') ?>"
        >
    </label>
    <label>
        Genre
        <input
            type="text"
            name="genre"
            placeholder="genre"
            value="<?= $success ? '' : htmlspecialchars($_POST['genre'] ?? '') ?>"
        >
    </label>
    <label>
        Duration
        <input
            type="text"
            name="duration"
            placeholder="..h ..m"
            value="<?= $success ? '' : htmlspecialchars($_POST['duration'] ?? '') ?>"
        >
    </label>
    <label>
        Year
        <input
            type="number"
            name="year"
            min="1888"
            max="2100"
            placeholder="...."
            value="<?= $success ? '' : htmlspecialchars($_POST['year'] ?? '') ?>"
        >
    </label>
    <label>
        Description
        <textarea
            name="description"
            rows="4"
        ><?= $success ? '' : htmlspecialchars($_POST['description'] ?? '') ?></textarea>
    </label>
    <button
        type="submit"
        class="saveMovieBtn"
    >
        Add Movie
    </button>
</form>
    <hr>

    <h2>Films beheren</h2>

    <input
        type="text"
        id="movieSearch"
        placeholder="Zoek film..."
    >

    <?php
    $movies = Movie::getAll($conn);
    ?>

    <ul id="movieList">

<?php foreach($movies as $movie): ?>

<li class="movieItem">

    <div class="movieInfo">
        <span class="movieId">
            #<?= $movie->id ?>
        </span>

        <span class="movieTitle">
            <?= htmlspecialchars($movie->title) ?>
        </span>
    </div>

    <form
        method="POST"
        onsubmit="return confirmDelete(
            '<?= htmlspecialchars(addslashes($movie->title)) ?>'
        )"
    >

        <input
            type="hidden"
            name="delete_id"
            value="<?= $movie->id ?>"
        >

        <button
            type="submit"
            class="deleteBtn"
        >
            Verwijderen
        </button>

    </form>

</li>

<?php endforeach; ?>

</ul>
<?php
include_once('./include/footer.php')
?>
</body>
</html>