<?php
include_once('./include/connect.php');
if ($DBerr == true) {
    header('location: databaseError.php');
    exit;
}
session_start();

require_once('./include/movie.php');

// Haal alle films op
$movies = Movie::getAll($conn);

// Filter genres voor dropdown
$genres = array_unique(array_filter(array_map(fn($m) => $m->genre, $movies)));
sort($genres);
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

<?php include_once('./include/header.php'); ?>

<!-- top movie (featured) -->
<section class="movie-hero">
  <article class="movie-inner">
    <figure class="poster">
      <img src="./images/movie_posters/five nights at freddy's 2.png"
           alt="Five Nights at Freddy's 2 poster"
           onerror="this.style.display='none'">
    </figure>
    <section class="movie-info">
      <h1 class="movie-title">Five Nights at Freddy's 2</h1>
      <ul class="movie-meta">
        <li><span class="badge age">16+</span></li>
        <li><span class="badge">Horror</span></li>
        <li><span class="badge">1u 44m</span></li>
        <li><span class="badge">2025</span></li>
      </ul>
      <p class="description">Een nieuwe beveiliger begint zijn eerste nacht in Freddy Fazbear's Pizza, waar de animatronics vrijer ronddwalen dan ooit tevoren.</p>
    </section>
  </article>
</section>

<section class="ticket-wrap">
  <button class="btn-ticket" onclick="document.getElementById('showtimes').classList.toggle('visible')">
    koop ticket
  </button>
</section>

<section class="showtimes" id="showtimes">
  <time class="showtime-date">Vandaag</time>
  <ul class="times-grid">
    <li><button class="time-btn" onclick="goToReserve('five nights at freddy\'s 2','13:15')">13:15</button></li>
    <li><button class="time-btn" onclick="goToReserve('five nights at freddy\'s 2','15:45')">15:45</button></li>
    <li><button class="time-btn" onclick="goToReserve('five nights at freddy\'s 2','18:00')">18:00</button></li>
    <li><button class="time-btn" onclick="goToReserve('five nights at freddy\'s 2','20:30')">20:30</button></li>
    <li><button class="time-btn" onclick="goToReserve('five nights at freddy\'s 2','22:50')">22:50</button></li>
  </ul>
</section>

<hr class="divider">

<!-- Filters -->
<section class="filter-bar">
  <!-- Locaties dropdown -->
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

  <!-- Genre filter -->
  <div class="genre-bar">
    <label for="genreSelect">Genre:</label>
    <select id="genreSelect">
      <option value="">Alle genres</option>
      <?php foreach ($genres as $g): ?>
      <option value="<?= htmlspecialchars($g) ?>"><?= htmlspecialchars($g) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <!-- Zoekbalk (klanten) -->
  <div class="search-bar-wrap">
    <input type="text" id="movieSearchHome" placeholder="Film zoeken...">
  </div>
</section>

<!-- movies list -->
<section class="locations-section">
<ul class="movies-scroll" id="movieGrid">
    <?php foreach ($movies as $movie): ?>
    <li class="movie-card"
        data-id="<?= $movie->id ?>"
        data-title="<?= htmlspecialchars($movie->title) ?>"
        data-poster="<?= htmlspecialchars($movie->posterPath ?? '') ?>"
        data-age="<?= htmlspecialchars($movie->ageRating ?? '') ?>"
        data-genre="<?= htmlspecialchars($movie->genre ?? '') ?>"
        data-duration="<?= htmlspecialchars($movie->duration ?? '') ?>"
        data-year="<?= htmlspecialchars($movie->year ?? '') ?>"
        data-description="<?= htmlspecialchars($movie->description ?? '') ?>">
        <figure class="movie-card-thumb">
            <img src="<?= htmlspecialchars($movie->posterPath ?? '') ?>"
                 alt="<?= htmlspecialchars($movie->posterAlt ?? $movie->title) ?>"
                 onerror="this.src='./images/placeholder.png';">
        </figure>
        <h2 class="movie-card-title"><?= htmlspecialchars($movie->title) ?></h2>
    </li>
    <?php endforeach; ?>
</ul>
</section>

<div class="divider"></div>

<!-- Film modal -->
<div id="movieModal" class="movie-modal">
    <div class="movie-modal-content">
        <button id="closeModal" aria-label="Sluiten">✖</button>
        <div class="modal-body">
            <img id="modalPoster" src="" alt="" class="modal-poster-img">
            <div class="modal-info">
                <h2 id="modalTitle"></h2>
                <div id="modalMeta"></div>
                <p id="modalDescription"></p>
                <button id="showTicketsBtn">Ticket boeken</button>
                <div id="modalShowtimes" style="display:none;">
                    <p class="showtimes-label">Kies een tijdstip:</p>
                    <button class="time-btn modal-time" data-time="13:15">13:15</button>
                    <button class="time-btn modal-time" data-time="15:45">15:45</button>
                    <button class="time-btn modal-time" data-time="18:00">18:00</button>
                    <button class="time-btn modal-time" data-time="20:30">20:30</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('./include/footer.php'); ?>
<script type="module" src="./src/app.js"></script>
</body>
</html>
