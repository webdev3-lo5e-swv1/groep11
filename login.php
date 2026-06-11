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
    <?php include_once('./include/header.php'); ?>
<form method="POST">

    <input
        type="text"
        name="username"
        placeholder="Username"
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
    <button type="submit">
        Login
    </button>
    <br> <br>
    <a href="register.php">create account?</a>

</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare(
        "SELECT * FROM users WHERE username = ?"
    );

    $stmt->execute([$username]);

    $user = $stmt->fetch();
    if (
        $user &&
        password_verify(
            $password,
            $user['password']
        )
    ) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header('Location: index.php');
        exit;
    }

    echo "Onjuiste logingegevens";
}
?>
</body>
</html>
