<?php
require_once __DIR__ . '/Model.php';

class Movie extends Model
{
    private string  $title;
    private ?string $posterPath;
    private ?string $posterAlt;
    private ?string $ageRating;
    private ?string $genre;
    private ?string $duration;
    private ?int    $year;
    private ?string $description;

    public function __construct(
        string  $title,
        ?int    $id          = null,
        ?string $posterPath  = null,
        ?string $posterAlt   = null,
        ?string $ageRating   = null,
        ?string $genre       = null,
        ?string $duration    = null,
        ?int    $year        = null,
        ?string $description = null
    ) {
        $this->id          = $id;
        $this->title       = $title;
        $this->posterPath  = $posterPath;
        $this->posterAlt   = $posterAlt;
        $this->ageRating   = $ageRating;
        $this->genre       = $genre;
        $this->duration    = $duration;
        $this->year        = $year;
        $this->description = $description;
    }

    // ── Getters ──────────────────────────────────────────────────────────────
    public function getTitle():       string  { return $this->title; }
    public function getPosterPath():  ?string { return $this->posterPath; }
    public function getPosterAlt():   ?string { return $this->posterAlt; }
    public function getAgeRating():   ?string { return $this->ageRating; }
    public function getGenre():       ?string { return $this->genre; }
    public function getDuration():    ?string { return $this->duration; }
    public function getYear():        ?int    { return $this->year; }
    public function getDescription(): ?string { return $this->description; }

    // ── Backwards-compat public property access (voor bestaande views) ──────
    public function __get(string $name)
    {
        $map = [
            'id'          => 'id',
            'title'       => 'title',
            'posterPath'  => 'posterPath',
            'posterAlt'   => 'posterAlt',
            'ageRating'   => 'ageRating',
            'genre'       => 'genre',
            'duration'    => 'duration',
            'year'        => 'year',
            'description' => 'description',
        ];
        if (isset($map[$name])) {
            return $this->{$map[$name]};
        }
        return null;
    }

    // ── CRUD ─────────────────────────────────────────────────────────────────
    public function save(PDO $conn): int|false
    {
        $sql = "INSERT INTO movies
                    (title, poster_path, poster_alt, age_rating, genre, duration, year, description)
                VALUES
                    (:title, :posterPath, :posterAlt, :ageRating, :genre, :duration, :year, :description)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->execute([
            ':title'       => $this->title,
            ':posterPath'  => $this->posterPath,
            ':posterAlt'   => $this->posterAlt,
            ':ageRating'   => $this->ageRating,
            ':genre'       => $this->genre,
            ':duration'    => $this->duration,
            ':year'        => $this->year,
            ':description' => $this->description,
        ]);

        return (int) $conn->lastInsertId();
    }

    public static function update(PDO $conn, int $id, array $data): bool
    {
        $stmt = $conn->prepare("
            UPDATE movies SET
                title       = :title,
                age_rating  = :ageRating,
                genre       = :genre,
                duration    = :duration,
                year        = :year,
                description = :description
            WHERE id = :id
        ");
        return $stmt->execute([
            ':title'       => $data['title'],
            ':ageRating'   => $data['age_rating'],
            ':genre'       => $data['genre'],
            ':duration'    => $data['duration'],
            ':year'        => $data['year'] ?: null,
            ':description' => $data['description'],
            ':id'          => $id,
        ]);
    }

    public static function getById(PDO $conn, int $id): ?self
    {
        $stmt = $conn->prepare("SELECT * FROM movies WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        return new self(
            title:       $row['title'],
            id:          (int)$row['id'],
            posterPath:  $row['poster_path'],
            posterAlt:   $row['poster_alt'],
            ageRating:   $row['age_rating'],
            genre:       $row['genre'],
            duration:    $row['duration'],
            year:        $row['year'] !== null ? (int)$row['year'] : null,
            description: $row['description']
        );
    }

    public static function getAll(PDO $conn): array
    {
        $stmt = $conn->prepare("SELECT * FROM movies ORDER BY title ASC");
        $stmt->execute();

        $movies = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $movies[] = new self(
                title:       $row['title'],
                id:          (int)$row['id'],
                posterPath:  $row['poster_path'],
                posterAlt:   $row['poster_alt'],
                ageRating:   $row['age_rating'],
                genre:       $row['genre'],
                duration:    $row['duration'],
                year:        $row['year'] !== null ? (int)$row['year'] : null,
                description: $row['description']
            );
        }
        return $movies;
    }

    public static function delete(PDO $conn, int $id): bool
    {
        return self::deleteById($conn, 'movies', $id);
    }

    public function render(): string
    {
        $posterPath  = htmlspecialchars('./images/movie_posters/' . $this->title . '.png');
        $posterAlt   = htmlspecialchars($this->title . ' poster');
        $title       = htmlspecialchars($this->title);
        $rating      = htmlspecialchars($this->ageRating  ?? '');
        $genre       = htmlspecialchars($this->genre      ?? '');
        $duration    = htmlspecialchars($this->duration   ?? '');
        $year        = htmlspecialchars((string)($this->year ?? ''));
        $description = htmlspecialchars($this->description ?? '');

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
                <p class="description">{$description}</p>
            </section>
        </article>
        HTML;
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'posterPath'  => $this->posterPath,
            'ageRating'   => $this->ageRating,
            'genre'       => $this->genre,
            'duration'    => $this->duration,
            'year'        => $this->year,
            'description' => $this->description,
        ];
    }
}
