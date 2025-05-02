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
        $env = parse_ini_file('.env');

        $this->driver = $env['DB_DRIVER'] ?? 'mysql';
        $this->host = $env['DB_HOST'] ?? '127.0.0.1';
        $this->user = $env['DB_USER'] ?? 'root';
        $this->pass = $env['DB_PASS'] ?? 'root';
        $this->database = $env['DB_NAME'] ?? 'vouch';
        $this->port = $env['DB_PORT'] ?? 3306;
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