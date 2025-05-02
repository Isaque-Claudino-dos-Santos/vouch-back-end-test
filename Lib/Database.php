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

    public function __construct(Env $env)
    {
        $this->driver = $env->get('DB_DRIVER', 'mysql');
        $this->host = $env->get('DB_HOST', '127.0.0.1');
        $this->user = $env->get('DB_USER', 'root');
        $this->pass = $env->get('DB_PASS', 'root');
        $this->database = $env->get('DB_NAME', 'vouch');
        $this->port = $env->get('DB_PORT', 3306);
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