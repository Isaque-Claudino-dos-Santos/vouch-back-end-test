<?php

namespace Lib;

readonly class Env
{
    private array $env;

    public function __construct(string $file)
    {
        $this->env = $this->getEnvFromFile($file);
    }

    private function getEnvFromFile(string $file): array
    {
        if (!file_exists($file)) {
            throw new \Exception("Env file '$file' not found.");
        }

        return parse_ini_file($file) ?? [];

    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->env[$key] ?? $default;
    }

}