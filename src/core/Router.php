<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $callback): void
    {
        $this->routes['GET'][$path] = $callback;
    }

    public function post(string $path, callable $callback): void
    {
        $this->routes['POST'][$path] = $callback;
    }

    public function resolve(Request $request, Response $response): void
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        $callback = $this->routes[$method][$path] ?? null;

        if (!$callback) {
            $response->json(['error' => 'Not Found'], 404);
            return;
        }

        call_user_func($callback, $request, $response);
    }
}