<?php

declare(strict_types=1);

namespace VM\Infrastructure\Route;

use VM\Infrastructure\Http\BaseRequest;
use VM\Infrastructure\Http\Response\Response;

class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, callable $handler): self
    {
        $this->routes[] = [$method, $pattern, $handler];

        return $this;
    }

    /**
     * @throws JsonException
     */
    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as [$m, $pattern, $handler]) {
            if ($method !== $m) {
                continue;
            }

            $regex = preg_replace(
                '#\{(\w+)}#',
                '(?P<\1>[^/]+)',
                $pattern,
            );

            $regex = "#^{$regex}$#";

            if (preg_match($regex, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $request = BaseRequest::capture($params);

                $response = $handler($request);

                if ($response instanceof Response) {
                    $response->send();
                } else {
                    header('Content-Type: application/json');
                    echo json_encode($response, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
                }

                return;
            }
        }

        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Route not found'], JSON_THROW_ON_ERROR);
    }
}
