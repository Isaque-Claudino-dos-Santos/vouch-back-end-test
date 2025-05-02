<?php

namespace Models;

use PDO;

class Message
{
    public int $id;
    public string $value;
    public string $created_at;
    public string $updated_at;

    public static function makeFromRow(array $row): Message
    {
        $message = new Message();
        $message->id = $row['id'];
        $message->value = $row['value'];
        $message->created_at = $row['created_at'];
        $message->updated_at = $row['updated_at'];
        return $message;
    }

    public static function save(PDO $pdo, string $message): void
    {
        $sql = "INSERT INTO messages (value) VALUES (:value)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':value', $message);
        $stmt->execute();
    }

    public static function all(PDO $pdo): array
    {
        $sql = "SELECT * FROM messages";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $dataFetch = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(fn($row) => self::makeFromRow($row), $dataFetch);
    }

    public static function messageExists(PDO $pdo, string $message): bool
    {
        $sql = "SELECT COUNT(*) FROM messages WHERE value = :value";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':value', $message);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $count > 0;
    }
}