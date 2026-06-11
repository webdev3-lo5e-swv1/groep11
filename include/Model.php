<?php
abstract class Model
{
    protected ?int $id;

    public function getId(): ?int
    {
        return $this->id;
    }

    abstract public function save(PDO $conn): int|false;

    public static function deleteById(PDO $conn, string $table, int $id): bool
    {
        $stmt = $conn->prepare("DELETE FROM `{$table}` WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
