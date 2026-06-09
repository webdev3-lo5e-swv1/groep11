<header>
  <a href="index.php"><figure class="logo-placeholder logo">MBO<br>CINEMAS</figure></a>
    <nav>
      <a href="index.php">Home</a>

      <?php if(isset($_SESSION['user_id'])&&$_SESSION['role'] == 'medewerker'): ?>
          <a href="logout.php">Uitloggen</a>
          <a href="medPagina.php">medewerker pagina</a>
      <?php elseif(isset($_SESSION['user_id'])&&$_SESSION['role'] !== 'medewerker'): ?>
          <a href="logout.php">Uitloggen</a>
      <?php else: ?>
          <a href="login.php">Login</a>
      <?php endif; ?>
    </nav>
</header>