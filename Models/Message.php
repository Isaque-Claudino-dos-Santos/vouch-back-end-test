<?php

namespace Models;

use PDO;

class Message
{
    public int $id;
    public string $value;
    public string $created_at;
    public string $updated_at;

    public function save(PDO $pdo, string $message): void
    {
        $sql = "INSERT INTO messages (value) VALUES (:value)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':value', $message);
        $stmt->execute();
    }

    public function messageExists(PDO $pdo, string $message): bool
    {
        $sql = "SELECT COUNT(*) FROM messages WHERE value = :value";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':value', $message);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $count > 0;
    }
}