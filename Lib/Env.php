<?php

namespace Lib;

readonly class Env
{
    private array $env;

    public function __construct(string $file)
    {
        $this->env = parse_ini_file($file);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->env[$key] ?? $default;
    }

}