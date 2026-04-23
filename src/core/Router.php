<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, $callback): void
    {
        $this->routes['GET'][$path] = $callback;
    }

    public function post(string $path, $callback): void
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
    
        if (is_array($callback)) {
            $controller = new $callback[0]();
            $methodName = $callback[1];
    
            call_user_func([$controller, $methodName], $request, $response);
            return;
        }
    
        call_user_func($callback, $request, $response);
    }
}