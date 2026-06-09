<?php
require_once('./include/connect.php');

class Movie
{
    public string  $title;
    public ?string $posterPath;
    public ?string $posterAlt;
    public ?string $ageRating;
    public ?string $genre;
    public ?string $duration;
    public ?int    $year;
    public ?string $synopsis;

    public function __construct(
        string  $title,
        ?string $posterPath = null,
        ?string $posterAlt  = null,
        ?string $ageRating  = null,
        ?string $genre      = null,
        ?string $duration   = null,
        ?int    $year       = null,
        ?string $synopsis   = null
    ) {
        $this->title      = $title;
        $this->posterPath = $posterPath;
        $this->posterAlt  = $posterAlt;
        $this->ageRating  = $ageRating;
        $this->genre      = $genre;
        $this->duration   = $duration;
        $this->year       = $year;
        $this->synopsis   = $synopsis;
    }

    public function save(PDO $conn): int|false
    {
        $sql = "INSERT INTO movies
                    (title, poster_path, poster_alt, age_rating, genre, duration, year, synopsis)
                VALUES (:title, :posterPath, :posterAlt, :ageRating, :genre, :duration, :year, :synopsis)";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            error_log("Prepare failed");
            return false;
        }

        $stmt->execute([
            ':title'      => $this->title,
            ':posterPath' => $this->posterPath,
            ':posterAlt'  => $this->posterAlt,
            ':ageRating'  => $this->ageRating,
            ':genre'      => $this->genre,
            ':duration'   => $this->duration,
            ':year'       => $this->year,
            ':synopsis'   => $this->synopsis,
        ]);

        return (int) $conn->lastInsertId();
    }

    public function render(): string
    {
    $posterPath = htmlspecialchars('./images/movie_posters/' . $this->title . '.png');
    $posterAlt  = htmlspecialchars($this->title . ' poster');
    $title      = htmlspecialchars($this->title);
    $rating     = htmlspecialchars($this->ageRating  ?? '');
    $genre      = htmlspecialchars($this->genre      ?? '');
    $duration   = htmlspecialchars($this->duration   ?? '');
    $year       = htmlspecialchars((string)($this->year ?? ''));
    $synopsis   = htmlspecialchars($this->synopsis   ?? '');

    return <<<HTML
    <article class="movie-inner">
        <figure class="poster">
            <img src="{$posterPath}" alt="{$posterAlt}" onerror="this.style.display='none'">
        </figure>
        <section class="movie-info">
            <h1 class="movie-title">{$title}</h1>
            <ul class="movie-meta">
                <li><span class="badge age">{$rating}</span></li>
                <li><span class="badge">{$genre}</span></li>
                <li><span class="badge">{$duration}</span></li>
                <li><span class="badge">{$year}</span></li>
            </ul>
            <p class="synopsis">{$synopsis}</p>
        </section>
    </article>
    HTML;
    }
    public static function getAll(PDO $conn): array
    {
        $stmt = $conn->query("SELECT * FROM movies ORDER BY title ASC");

        if (!$stmt) {
            error_log("Query failed");
            return [];
        }

        $movies = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $movies[] = new self(
                title:      $row['title'],
                posterPath: $row['poster_path'],
                posterAlt:  $row['poster_alt'],
                ageRating:  $row['age_rating'],
                genre:      $row['genre'],
                duration:   $row['duration'],
                year:       $row['year'] !== null ? (int)$row['year'] : null,
                synopsis:   $row['synopsis']
            );
        }

        return $movies;
    }
}