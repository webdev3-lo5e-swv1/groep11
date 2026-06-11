<?php
require_once __DIR__ . '/Model.php';

class Reservation extends Model
{
    private int     $user_id;
    private int     $movie_id;
    private ?int    $showtime_id;
    private ?string $showtime_text;
    private int     $tickets;
    private string  $status;

    public function __construct(
        int     $user_id,
        int     $movie_id,
        int     $tickets,
        ?int    $showtime_id   = null,
        ?string $showtime_text = null,
        string  $status        = 'active',
        ?int    $id            = null
    ) {
        $this->id            = $id;
        $this->user_id       = $user_id;
        $this->movie_id      = $movie_id;
        $this->showtime_id   = $showtime_id;
        $this->showtime_text = $showtime_text;
        $this->tickets       = $tickets;
        $this->status        = $status;
    }

    // ── Getters ──────────────────────────────────────────────────────────────
    public function getUserId():      int     { return $this->user_id; }
    public function getMovieId():     int     { return $this->movie_id; }
    public function getShowtimeId():  ?int    { return $this->showtime_id; }
    public function getShowtimeText():?string { return $this->showtime_text; }
    public function getTickets():     int     { return $this->tickets; }
    public function getStatus():      string  { return $this->status; }

    // ── CRUD ─────────────────────────────────────────────────────────────────
    public function save(PDO $conn): int|false
    {
        $stmt = $conn->prepare("
            INSERT INTO reservations
                (user_id, movie_id, showtime_id, showtime_text, tickets, status)
            VALUES
                (:user_id, :movie_id, :showtime_id, :showtime_text, :tickets, :status)
        ");
        $stmt->execute([
            ':user_id'       => $this->user_id,
            ':movie_id'      => $this->movie_id,
            ':showtime_id'   => $this->showtime_id,
            ':showtime_text' => $this->showtime_text,
            ':tickets'       => $this->tickets,
            ':status'        => $this->status,
        ]);
        return (int) $conn->lastInsertId();
    }

    public static function getByUser(PDO $conn, int $user_id): array
    {
        $stmt = $conn->prepare("
            SELECT r.*, m.title, m.poster_path, m.genre, m.duration,
                   COALESCE(s.start_time, r.showtime_text) AS display_time
            FROM reservations r
            JOIN movies m ON r.movie_id = m.id
            LEFT JOIN showtimes s ON r.showtime_id = s.id
            WHERE r.user_id = :user_id
            ORDER BY r.id DESC
        ");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAll(PDO $conn): array
    {
        $stmt = $conn->prepare("
            SELECT r.*, m.title, u.username,
                   COALESCE(s.start_time, r.showtime_text) AS display_time
            FROM reservations r
            JOIN movies m ON r.movie_id = m.id
            JOIN users u ON r.user_id = u.id
            LEFT JOIN showtimes s ON r.showtime_id = s.id
            ORDER BY r.id DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updateTickets(PDO $conn, int $id, int $tickets, int $user_id): bool
    {
        if ($tickets < 1 || $tickets > 10) return false;
        $stmt = $conn->prepare("
            UPDATE reservations SET tickets = :tickets
            WHERE id = :id AND user_id = :user_id AND status = 'active'
        ");
        return $stmt->execute([':tickets' => $tickets, ':id' => $id, ':user_id' => $user_id]);
    }

    public static function cancel(PDO $conn, int $id, int $user_id): bool
    {
        $stmt = $conn->prepare("
            UPDATE reservations SET status = 'cancelled'
            WHERE id = :id AND user_id = :user_id
        ");
        return $stmt->execute([':id' => $id, ':user_id' => $user_id]);
    }

    public static function cancelByEmployee(PDO $conn, int $id): bool
    {
        $stmt = $conn->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public static function updateTicketsByEmployee(PDO $conn, int $id, int $tickets): bool
    {
        if ($tickets < 1 || $tickets > 10) return false;
        $stmt = $conn->prepare("UPDATE reservations SET tickets = :tickets WHERE id = :id");
        return $stmt->execute([':tickets' => $tickets, ':id' => $id]);
    }
}
