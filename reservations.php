<?php
session_start();
include_once './include/connect.php';
if ($DBerr) { header('location: databaseError.php'); exit; }
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once './include/Reservation.php';
$user_id = (int) $_SESSION['user_id'];

// ── POST → verwerk → redirect (PRG) ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel_id'])) {
        Reservation::cancel($conn, (int)$_POST['cancel_id'], $user_id);
        header('Location: reservations.php?msg=cancelled');
        exit;
    }
    if (isset($_POST['update_id'])) {
        $ok = Reservation::updateTickets($conn, (int)$_POST['update_id'], (int)($_POST['new_tickets'] ?? 0), $user_id);
        header('Location: reservations.php?msg=' . ($ok ? 'updated' : 'error'));
        exit;
    }
}

$msg          = $_GET['msg'] ?? '';
$reservations = Reservation::getByUser($conn, $user_id);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MBO Cinemas – Mijn reserveringen</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./style/style.css">
</head>
<body>
<?php include_once './include/header.php'; ?>

<main class="reservations-wrap">
    <h1>Mijn reserveringen</h1>

    <?php if ($msg === 'cancelled'): ?><p class="res-message">Reservering geannuleerd.</p><?php endif; ?>
    <?php if ($msg === 'updated'):   ?><p class="res-message">Tickets bijgewerkt.</p><?php endif; ?>
    <?php if ($msg === 'error'):     ?><p style="color:#e63946;">Bijwerken mislukt. Controleer het aantal (1–10).</p><?php endif; ?>

    <?php if (empty($reservations)): ?>
        <p>Je hebt nog geen reserveringen. <a href="index.php">Bekijk films</a></p>
    <?php else: ?>
    <table class="res-table">
        <thead>
            <tr><th>#</th><th>Film</th><th>Tijdstip</th><th>Tickets</th><th>Status</th><th>Acties</th></tr>
        </thead>
        <tbody>
        <?php foreach ($reservations as $r): ?>
            <tr class="res-row <?= $r['status'] === 'cancelled' ? 'cancelled-row' : '' ?>">
                <td><?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['title']) ?></td>
                <td><?= $r['display_time'] ? htmlspecialchars($r['display_time']) : '–' ?></td>
                <td><?= $r['tickets'] ?></td>
                <td><?= $r['status'] === 'active' ? 'Actief' : 'Geannuleerd' ?></td>
                <td>
                <?php if ($r['status'] === 'active'): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="update_id" value="<?= $r['id'] ?>">
                        <input type="number" name="new_tickets" value="<?= $r['tickets'] ?>" min="1" max="10" style="width:3.5rem;">
                        <button type="submit" class="btn-small">Wijzigen</button>
                    </form>
                    <form method="POST" style="display:inline;"
                          onsubmit="return confirm('Reservering #<?= $r['id'] ?> annuleren?')">
                        <input type="hidden" name="cancel_id" value="<?= $r['id'] ?>">
                        <button type="submit" class="btn-small btn-danger">Annuleren</button>
                    </form>
                <?php else: ?>
                    <span style="color:#888;">–</span>
                <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</main>

<?php include_once './include/footer.php'; ?>
</body>
</html>
