<?php

namespace Lib;

readonly class Database
{
    private string $driver;
    private string $host;
    private string $user;
    private string $pass;
    private string $database;
    private int $port;

    public function __construct()
    {

        $this->driver = 'mysql';
        $this->host = 'localhost';
        $this->user = 'test';
        $this->pass = 'Test@123';
        $this->database = 'vouch';
        $this->port = 3306;
    }

    private function getDNS(): string
    {
        return "{$this->driver}:host={$this->host};port={$this->port};dbname={$this->database}";
    }

    public function connect(): \PDO
    {
        return new \PDO($this->getDNS(), $this->user, $this->pass);
    }
}