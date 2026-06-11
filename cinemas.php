<?php
session_start();
include_once('./include/connect.php');
if ($DBerr == true) {
    header('location: databaseError.php');
    exit;
}

$stmt = $conn->query("SELECT * FROM cinemas ORDER BY city ASC");
$cinemas = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MBO Cinemas – Bioscopen</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./style/style.css">
</head>
<body>
<?php include_once('./include/header.php'); ?>

<main class="cinemas-wrap">
    <h1>Onze bioscopen</h1>

    <?php if (empty($cinemas)): ?>
        <p>Momenteel zijn er geen locaties beschikbaar.</p>
    <?php else: ?>
    <ul class="cinemas-list">
        <?php foreach ($cinemas as $cinema): ?>
        <li class="cinema-card">
            <h2><?= htmlspecialchars($cinema['name']) ?></h2>
            <p>📍 <?= htmlspecialchars($cinema['city']) ?></p>
            <a href="index.php?locatie=<?= urlencode($cinema['city']) ?>" class="btn-small">Films in <?= htmlspecialchars($cinema['city']) ?></a>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</main>

<?php include_once('./include/footer.php'); ?>
</body>
</html>
