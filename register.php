<?php
include_once('./include/connect.php');
if ($DBerr == true) {
    header('location: databaseError.php');
}
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $medcode  = trim($_POST['medcode']  ?? '');

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Vul alle verplichte velden in.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $role = ($medcode === 'MOVIE2025') ? 'medewerker' : 'user';

        $stmt = $conn->prepare("
            INSERT INTO users (username, email, password, role)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$username, $email, $hash, $role]);

        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MBO Cinemas – Registreren</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./style/style.css">
<style>
  .auth-page {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem 1rem;
  }
  .auth-card {
    background: linear-gradient(160deg, #0d1b3e, #081428);
    border: 1px solid #1c2f68;
    border-radius: 16px;
    padding: 2.5rem 2rem;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 8px 40px rgba(0,0,0,.5);
  }
  .auth-logo {
    text-align: center;
    margin-bottom: 1.75rem;
  }
  .auth-logo img {
    height: 56px;
    filter: drop-shadow(0 0 8px rgba(232,184,75,.3));
  }
  .auth-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 2rem;
    letter-spacing: .06em;
    color: var(--gold);
    text-align: center;
    margin-bottom: 1.75rem;
  }
  .auth-field {
    display: flex;
    flex-direction: column;
    gap: .35rem;
    margin-bottom: 1.1rem;
  }
  .auth-field label {
    font-size: .82rem;
    font-weight: 600;
    letter-spacing: .05em;
    text-transform: uppercase;
    color: #aab;
  }
  .auth-field input {
    background: rgba(255,255,255,.05);
    border: 1px solid #1c2f68;
    border-radius: 8px;
    padding: .7rem 1rem;
    color: #eee;
    font-size: .95rem;
    font-family: inherit;
    transition: border-color .2s;
    width: 100%;
  }
  .auth-field input:focus {
    outline: none;
    border-color: var(--gold);
    background: rgba(255,255,255,.07);
  }
  .auth-field .auth-hint {
    font-size: .78rem;
    color: #668;
    margin-top: .1rem;
  }
  .auth-error {
    background: rgba(230,57,70,.15);
    border: 1px solid rgba(230,57,70,.4);
    border-radius: 8px;
    color: #f87;
    padding: .65rem 1rem;
    font-size: .88rem;
    margin-bottom: 1.1rem;
  }
  .auth-divider {
    border: none;
    border-top: 1px solid #1c2f68;
    margin: 1.25rem 0;
  }
  .auth-submit {
    width: 100%;
    background: var(--gold);
    color: #081428;
    border: none;
    border-radius: 10px;
    padding: .85rem;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 1.15rem;
    letter-spacing: .1em;
    cursor: pointer;
    margin-top: .5rem;
    transition: background .2s, transform .15s;
  }
  .auth-submit:hover { background: #d4a017; transform: translateY(-1px); }
  .auth-submit:active { transform: translateY(0); }
  .auth-footer-link {
    text-align: center;
    margin-top: 1.25rem;
    font-size: .88rem;
    color: #889;
  }
  .auth-footer-link a { color: var(--gold); text-decoration: none; }
  .auth-footer-link a:hover { text-decoration: underline; }
</style>
</head>
<body style="display:flex; flex-direction:column; min-height:100vh;">

<?php include_once('./include/header.php'); ?>

<div class="auth-page">
  <div class="auth-card">

    <div class="auth-logo">
      <img src="./images/logo.png" alt="MBO Cinemas logo"
           onerror="this.style.display='none'">
    </div>

    <h1 class="auth-title">Account aanmaken</h1>

    <?php if ($error): ?>
      <div class="auth-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="auth-field">
        <label for="username">Gebruikersnaam *</label>
        <input type="text" id="username" name="username"
               placeholder="kies een gebruikersnaam" required autocomplete="username"
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
      </div>
      <div class="auth-field">
        <label for="email">E-mailadres *</label>
        <input type="email" id="email" name="email"
               placeholder="jouw@email.nl" required autocomplete="email"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="auth-field">
        <label for="password">Wachtwoord *</label>
        <input type="password" id="password" name="password"
               placeholder="••••••••" required autocomplete="new-password">
      </div>

      <hr class="auth-divider">

      <div class="auth-field">
        <label for="medcode">Medewerkercode</label>
        <input type="text" id="medcode" name="medcode"
               placeholder="optioneel — alleen voor medewerkers">
        <span class="auth-hint">Heb je een medewerkercode? Vul die hier in om medewerkerstoegang te krijgen.</span>
      </div>

      <button type="submit" class="auth-submit">Account aanmaken</button>
    </form>

    <p class="auth-footer-link">
      Al een account? <a href="login.php">Inloggen</a>
    </p>

  </div>
</div>

<?php include_once('./include/footer.php'); ?>
</body>
</html>
