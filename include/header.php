<header>
    <a href="index.php" class="logo">
        <img class='logo_img' src="./images/logo.png" alt="MBO CINEMAS">
    </a>
    <nav>
        <a href="index.php">Home</a>
        <a href="cinemas.php">Bioscopen</a>
    <?php if(isset($_SESSION['user_id'])&&$_SESSION['role'] == 'medewerker'): ?>
        <a href="medPagina.php">medewerker pagina</a>
        <a href="showtimes.php">Zaalplanning</a>
        <a href="manage_reservations.php">Reserveringen beheer</a>
    <?php endif; ?>
    <?php if(isset($_SESSION['user_id'])): ?>
        <a href="reservation_create.php">Reserveren</a>
        <a href="reservations.php">Mijn reserveringen</a>
        <a href="logout.php">Uitloggen</a>
    <?php else: ?>
        <a href="login.php">Login</a>
    <?php endif; ?>
        
</nav>
</header>