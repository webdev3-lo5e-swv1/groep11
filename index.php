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

<header>
  <figure class="logo-placeholder">MBO<br>CINEMAS</figure>
  <nav>
    <a href="#">Films</a>
    <a href="#">Agenda</a>
    <a href="#">Info</a>
  </nav>
</header>
 
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
<ul class="movies-scroll">
    <li class="movie-card">
      <figure class="movie-card-thumb">
        <img src="./images/movie_posters/fnaf2_Mangle_Poster.png" alt="FNAF 2" onerror="this.src='./images/placeholder.png';">
      </figure>
      <h2 class="movie-card-title">Five Nights at Freddy's 2</h2>
    </li>
    <li class="movie-card">
      <figure class="movie-card-thumb">
        <img src="" alt="Sinners" onerror="this.src='./images/placeholder.png';">
      </figure>
      <h2 class="movie-card-title">Sinners</h2>
    </li>
    <li class="movie-card">
      <figure class="movie-card-thumb">
        <img src="" alt="Mission Impossible" onerror="this.src='./images/placeholder.png';">
      </figure>
      <h2 class="movie-card-title">Mission: Impossible – The Final Reckoning</h2>
    </li>
    <li class="movie-card">
      <figure class="movie-card-thumb">
        <img src="" alt="Thunderbolts" onerror="this.src='./images/placeholder.png';">
      </figure>
      <h2 class="movie-card-title">Thunderbolts*</h2>
    </li>
    <li class="movie-card">
      <figure class="movie-card-thumb">
        <img src="" alt="A Minecraft Movie" onerror="this.src='./images/placeholder.png';">
      </figure>
      <h2 class="movie-card-title">A Minecraft Movie</h2>
    </li>
    <li class="movie-card">
      <figure class="movie-card-thumb">
        <img src="" alt="The Accountant 2" onerror="this.src='./images/placeholder.png';">
      </figure>
      <h2 class="movie-card-title">The Accountant 2</h2>
    </li>
    <li class="movie-card">
      <figure class="movie-card-thumb">
        <img src="" alt="Final Destination Bloodlines" onerror="this.src='./images/placeholder.png';">
      </figure>
      <h2 class="movie-card-title">Final Destination: Bloodlines</h2>
    </li>
    <li class="movie-card">
      <figure class="movie-card-thumb">
        <img src="" alt="Warfare" onerror="this.src='./images/placeholder.png';">
      </figure>
      <h2 class="movie-card-title">Warfare</h2>
    </li>
    <li class="movie-card">
      <figure class="movie-card-thumb">
        <img src="" alt="How to Train Your Dragon" onerror="this.src='./images/placeholder.png';">
      </figure>
      <h2 class="movie-card-title">How to Train Your Dragon</h2>
    </li>
    <li class="movie-card">
      <figure class="movie-card-thumb">
        <img src="" alt="28 Years Later" onerror="this.src='./images/placeholder.png';">
      </figure>
      <h2 class="movie-card-title">28 Years Later</h2>
    </li>
    <li class="movie-card">
      <figure class="movie-card-thumb">
        <img src="" alt="Jurassic World Rebirth" onerror="this.src='./images/placeholder.png';">
      </figure>
      <h2 class="movie-card-title">Jurassic World Rebirth</h2>
    </li>
    <li class="movie-card">
      <figure class="movie-card-thumb">
        <img src="" alt="Superman" onerror="this.src='./images/placeholder.png';">
      </figure>
      <h2 class="movie-card-title">Superman</h2>
    </li>
  </ul>
</section>
 
<div class="divider"></div>
 
<footer>
  <strong>Contact</strong>
  071-6103029<br>
  <a href="mailto:support@mbocinemas.nl">support@mbocinemas.nl</a><br>
  Gortestraat 109, 2311 KC Leiden
  <a id="mederwerker-link">mederwerker pagina</a>
</footer>

<script src='./src/app.js' defer></script>
</body>
</html>
