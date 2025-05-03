<?php

namespace Models;

use PDO;

class Message
{
    public int $id;
    public string $value;
    public string $createdAt;
    public string $updatedAt;

    private function makeFromRow(array $row): Message
    {
        $message = new Message();
        $message->id = $row['id'];
        $message->value = $row['value'];
        $message->createdAt = $row['created_at'];
        $message->updatedAt = $row['updated_at'];
        return $message;
    }

    public function findByValue(PDO $pdo, string $value): Message|null
    {
        $sql = "SELECT * FROM messages WHERE value = :value";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        $fetch = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!count($fetch)) {
            return null;
        }

        return $this->makeFromRow($fetch[0]);
    }

    public function save(PDO $pdo, string $message): Message|null
    {
        $sql = "INSERT INTO messages (value) VALUES (:value)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':value', $message);
        $stmt->execute();

        return $this->findByValue($pdo, $message);
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