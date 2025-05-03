<?php

namespace Models;

use PDO;

class Message
{
    public int $id;
    public string $value;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(
        private readonly PDO $pdo
    )
    {
    }

    private function makeFromRow(array $row): Message
    {
        $message = new Message($this->pdo);
        $message->id = $row['id'];
        $message->value = $row['value'];
        $message->createdAt = $row['created_at'];
        $message->updatedAt = $row['updated_at'];
        return $message;
    }

    public function findByValue(string $value): Message|null
    {
        $sql = "SELECT * FROM messages WHERE value = :value";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        $fetch = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!count($fetch)) {
            return null;
        }

        return $this->makeFromRow($fetch[0]);
    }

    public function save(string $message): Message|null
    {
        $sql = "INSERT INTO messages (value) VALUES (:value)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':value', $message);
        $stmt->execute();

        return $this->findByValue($message);
    }

    public function messageExists(string $message): bool
    {
        $sql = "SELECT COUNT(*) FROM messages WHERE value = :value";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':value', $message);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $count > 0;
    }
}