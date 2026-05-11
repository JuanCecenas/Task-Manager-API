<?php

namespace App\Core;

class Router
{
    /** @var array<string, array{static: array<string, mixed>, dynamic: list<array{regex: string, names: array<int, string>, callback: mixed}>}> */
    private array $routes = [];

    public function get(string $path, $callback): void
    {
        $this->addRoute('GET', $path, $callback);
    }

    public function post(string $path, $callback): void
    {
        $this->addRoute('POST', $path, $callback);
    }

    public function put(string $path, $callback): void
    {
        $this->addRoute('PUT', $path, $callback);
    }

    public function patch(string $path, $callback): void
    {
        $this->addRoute('PATCH', $path, $callback);
    }

    public function delete(string $path, $callback): void
    {
        $this->addRoute('DELETE', $path, $callback);
    }

    private function addRoute(string $method, string $path, $callback): void
    {
        if (!isset($this->routes[$method])) {
            $this->routes[$method] = ['static' => [], 'dynamic' => []];
        }

        if (!str_contains($path, '{')) {
            $this->routes[$method]['static'][$path] = $callback;

            return;
        }

        $compiled = $this->compileDynamicRoute($path);
        $this->routes[$method]['dynamic'][] = [
            'regex' => $compiled['regex'],
            'names' => $compiled['names'],
            'callback' => $callback,
        ];
    }

    /**
     * @return array{regex: string, names: array<int, string>}
     */
    private function compileDynamicRoute(string $path): array
    {
        $trimmed = trim($path, '/');
        if ($trimmed === '') {
            return ['regex' => '#^/$#', 'names' => []];
        }

        $names = [];
        $parts = [];
        foreach (explode('/', $trimmed) as $segment) {
            if (preg_match('/^\{([a-zA-Z_][a-zA-Z0-9_]*)\}$/', $segment, $m)) {
                $parts[] = '([^/]+)';
                $names[] = $m[1];
            } else {
                $parts[] = preg_quote($segment, '#');
            }
        }

        return [
            'regex' => '#^/' . implode('/', $parts) . '$#',
            'names' => $names,
        ];
    }

    public function resolve(Request $request, Response $response): void
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        if (!isset($this->routes[$method])) {
            $response->json(['error' => 'Not Found'], 404);

            return;
        }

        $callback = $this->routes[$method]['static'][$path] ?? null;

        if ($callback === null) {
            foreach ($this->routes[$method]['dynamic'] as $route) {
                if (preg_match($route['regex'], $path, $matches)) {
                    array_shift($matches);
                    foreach ($route['names'] as $i => $name) {
                        $request->setAttribute($name, $matches[$i] ?? null);
                    }
                    $callback = $route['callback'];
                    break;
                }
            }
        }

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
