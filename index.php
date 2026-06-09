<?php
include_once('./include/connect.php');
if ($DBerr == true){
  header('location: databaseError.php');
}

session_start();
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
include_once('./include/header.php');
?>
 
<!-- top movie -->
<section class="movie-hero">
  <article class="movie-inner">
    <figure class="poster">
      <img src="./images/movie_posters/fnaf2_Mangle_Poster.png"
           alt="Five Nights at Freddy's 2 poster"
           onerror="this.style.display='none'">
    </figure>
    <section class="movie-info">
      <h1 class="movie-title">Five Nights at Freddy's 2</h1>
      <ul class="movie-meta">
        <li><span class="badge age">16+</span></li>
        <li><span class="badge">Horror</span></li>
        <li><span class="badge">1u 54m</span></li>
        <li><span class="badge">2025</span></li>
      </ul>
      <p class="synopsis">Een nieuwe beveiliger begint zijn eerste nacht in Freddy Fazbear's Pizza, waar de animatronics vrijer ronddwalen dan ooit tevoren. Overleven is geen optie — het is een verplichting.</p>
    </section>
  </article>
</section>

<section class="ticket-wrap">
  <button class="btn-ticket" onclick="document.getElementById('showtimes').classList.toggle('visible')">
    koop ticket
  </button>
</section>
 
<section class="showtimes" id="showtimes">
  <time class="showtime-date">Vandaag — 18 mei 2026</time>
  <ul class="times-grid">
    <li><button class="time-btn">13:15</button></li>
    <li><button class="time-btn">15:45</button></li>
    <li><button class="time-btn">18:00</button></li>
    <li><button class="time-btn">20:30</button></li>
    <li><button class="time-btn">22:50</button></li>
  </ul>
</section>
 
<hr class="divider">
 
<!-- locations -->
<section class="locations-section">
  <div class="locations-bar" id="locationsBar">
    <span class="locations-label" id="locationsLabel">locaties</span>
    <button class="locations-arrow-btn" id="locationsArrow" aria-label="Kies locatie">›</button>
    <ul class="locations-dropdown" id="locationsDropdown">
      <li class="dropdown-option active" data-value="">Alle locaties</li>
      <li class="dropdown-option" data-value="leiden">Leiden</li>
      <li class="dropdown-option" data-value="amsterdam">Amsterdam</li>
      <li class="dropdown-option" data-value="rotterdam">Rotterdam</li>
      <li class="dropdown-option" data-value="denhaag">Den Haag</li>
      <li class="dropdown-option" data-value="utrecht">Utrecht</li>
    </ul>
  </div>
 
  <!-- movies list -->
<?php
require_once('./include/movie.php');
$movies = Movie::getAll($conn);
?>

<ul class="movies-scroll">
    <?php 
    foreach ($movies as $movie): 
    ?>
    <li class="movie-card">
        <figure class="movie-card-thumb">
            <img
                src="<?= htmlspecialchars($movie->posterPath ?? '') ?>"
                alt="<?= htmlspecialchars($movie->posterAlt ?? $movie->title) ?>"
                onerror="this.src='./images/placeholder.png';"
            >
        </figure>
        <h2 class="movie-card-title"><?= htmlspecialchars($movie->title) ?></h2>
    </li>
    <?php endforeach; ?>
</ul>
 
<div class="divider"></div>
 
<?php
include_once('./include/footer.php');
?>

<script src='./src/app.js' defer></script>
</body>
</html>
