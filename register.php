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
<form method="POST">

    <input
        type="text"
        name="username"
        placeholder="Username"
        required
    >
    <br>
    <input
        type="email"
        name="email"
        placeholder="Email"
        required
    >
    <br>
    <input
        type="password"
        name="password"
        placeholder="Password"
        required
    >
    <br>
    <input
        type="text"
        name="medcode"
        placeholder="Medewerkercode (optioneel)"
    >
    <br>
    <button type="submit">
        Registreren
    </button>

</form>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $medcode = trim($_POST['medcode']);

    $hash = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    $role = 'user';

    if ($medcode === 'MOVIE2025') {
        $role = 'medewerker';
    }

    $stmt = $conn->prepare("
        INSERT INTO users
        (username,email,password,role)
        VALUES
        (?,?,?,?)
    ");

    $stmt->execute([
        $username,
        $email,
        $hash,
        $role
    ]);

    header('Location: login.php');
    exit;
}
?>
</body>
</html>
