<?php
session_start();
include_once './include/connect.php';
if ($DBerr) { header('location: databaseError.php'); exit; }
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'medewerker') { header('Location: index.php'); exit; }

require_once './include/Reservation.php';

// ── PRG ───────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel_id'])) {
        Reservation::cancelByEmployee($conn, (int)$_POST['cancel_id']);
        header('Location: manage_reservations.php?msg=cancelled');
        exit;
    }
    if (isset($_POST['update_id'])) {
        $ok = Reservation::updateTicketsByEmployee($conn, (int)$_POST['update_id'], (int)($_POST['new_tickets'] ?? 0));
        header('Location: manage_reservations.php?msg=' . ($ok ? 'updated' : 'error'));
        exit;
    }
}

$msg          = $_GET['msg'] ?? '';
$reservations = Reservation::getAll($conn);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MBO Cinemas – Reserveringen beheer</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./style/style.css">
</head>
<body>
<?php include_once './include/header.php'; ?>

<main class="reservations-wrap">
    <h1>Reserveringen beheer</h1>

    <?php if ($msg === 'cancelled'): ?><p class="res-message">Reservering geannuleerd.</p><?php endif; ?>
    <?php if ($msg === 'updated'):   ?><p class="res-message">Tickets bijgewerkt.</p><?php endif; ?>
    <?php if ($msg === 'error'):     ?><p style="color:#e63946;">Bijwerken mislukt.</p><?php endif; ?>

    <input type="text" id="resSearch" placeholder="Zoek op gebruiker of film...">

    <?php if (empty($reservations)): ?>
        <p>Geen reserveringen gevonden.</p>
    <?php else: ?>
    <table class="res-table" id="resTable">
        <thead>
            <tr><th>#</th><th>Gebruiker</th><th>Film</th><th>Tijdstip</th><th>Tickets</th><th>Status</th><th>Acties</th></tr>
        </thead>
        <tbody>
        <?php foreach ($reservations as $r): ?>
            <tr class="res-row <?= $r['status'] === 'cancelled' ? 'cancelled-row' : '' ?>">
                <td><?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['username']) ?></td>
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
<script>
document.getElementById('resSearch').addEventListener('keyup', function() {
    const f = this.value.toLowerCase();
    document.querySelectorAll('.res-row').forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(f) ? '' : 'none';
    });
});
</script>
</body>
</html>
