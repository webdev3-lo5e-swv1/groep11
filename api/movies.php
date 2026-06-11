<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: same-origin');

require_once '../include/connect.php';
if ($DBerr) {
    http_response_code(503);
    echo json_encode(['error' => 'Database niet beschikbaar']);
    exit;
}

require_once '../include/movie.php';

$genre  = trim($_GET['genre']  ?? '');
$search = trim($_GET['search'] ?? '');

if ($genre !== '' || $search !== '') {
    $sql    = "SELECT * FROM movies WHERE 1=1";
    $params = [];

    if ($genre !== '') {
        $sql .= " AND LOWER(genre) LIKE LOWER(:genre)";
        $params[':genre'] = '%' . $genre . '%';
    }
    if ($search !== '') {
        $sql .= " AND LOWER(title) LIKE LOWER(:search)";
        $params[':search'] = '%' . $search . '%';
    }
    $sql .= " ORDER BY title ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $conn->prepare("SELECT * FROM movies ORDER BY title ASC");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$movies = array_map(fn($r) => [
    'id'          => (int)$r['id'],
    'title'       => $r['title'],
    'posterPath'  => $r['poster_path'],
    'ageRating'   => $r['age_rating'],
    'genre'       => $r['genre'],
    'duration'    => $r['duration'],
    'year'        => $r['year'],
    'description' => $r['description'],
], $rows);

echo json_encode(['movies' => $movies, 'count' => count($movies)]);
