<?php

namespace Lib;

readonly class Route
{
    public function __construct(
        public string $uri,
        public string $method,
        public array  $action,
    )
    {
    }


    public function wasRequested(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === $this->method && $_SERVER['REQUEST_URI'] === $this->uri;
    }

    public function callAction(): void
    {
        call_user_func([$this->action[0], $this->action[1]]);
    }
}