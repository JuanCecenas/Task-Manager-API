<?php

namespace App\Core;

class Request
{
    /** @var array<string, mixed> */
    private array $attributes = [];

    public function setAttribute(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->attributes[$name] ?? $default;
    }

    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }
        // Strip trailing slash so /health/ matches route /health (except root).
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/') ?: '/';
        }
        return $path;
    }

    public function getBody() : array
    {
        $body = [];
        if ($this->getMethod() === 'GET') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key);
            }
        }

        if (in_array($this->getMethod(), ['POST', 'PUT', 'PATCH'])) {
            $input = json_decode(file_get_contents('php://input'), true);

            if ($input) {
                $body = $input;
            }
        }
        
        return $body;
    }



}