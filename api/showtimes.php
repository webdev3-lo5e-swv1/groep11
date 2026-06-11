<?php
header('Content-Type: application/json');

require_once '../include/connect.php';
if ($DBerr) {
    http_response_code(503);
    echo json_encode(['error' => 'Database niet beschikbaar']);
    exit;
}

$movie_id = isset($_GET['movie_id']) ? (int)$_GET['movie_id'] : 0;

if ($movie_id <= 0) {
    echo json_encode(['showtimes' => []]);
    exit;
}

// Haal alle komende vertoningen op voor deze film, inclusief zaal en bioscoop
$stmt = $conn->prepare("
    SELECT
        s.id,
        DATE_FORMAT(s.start_time, '%H:%i') AS time,
        DATE_FORMAT(s.start_time, '%d-%m-%Y') AS date,
        s.start_time,
        h.name  AS hall,
        h.seats,
        c.name  AS cinema,
        c.city
    FROM showtimes s
    JOIN halls   h ON s.hall_id  = h.id
    JOIN cinemas c ON h.cinema_id = c.id
    WHERE s.movie_id = :movie_id
      AND s.start_time >= NOW()
    ORDER BY s.start_time ASC
    LIMIT 50
");
$stmt->execute([':movie_id' => $movie_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['showtimes' => $rows]);
