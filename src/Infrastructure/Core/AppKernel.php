<?php

declare(strict_types=1);

namespace VM\Infrastructure\Core;

use Dotenv\Dotenv;
use VM\Infrastructure\Container;
use VM\Infrastructure\Core\Constant\CoreConstant;
use VM\Infrastructure\Core\Database\Database;
use VM\Infrastructure\Core\Database\Model;
use VM\Infrastructure\Core\Exception\InvalidArgumentException;
use VM\Infrastructure\Http\BaseRequest;
use VM\Infrastructure\Route\Router;

class AppKernel
{
    private Container $container;
    private Router $router;

    public function __construct()
    {
        $this->container = new Container();
        $this->router = new Router();

        $this->loadEnv();
        $this->registerDatabase();
        $this->registerBindings();
        $this->registerRoutesFromContexts(CoreConstant::ROUTES_GLOB_PATTERN);
    }

    private function loadEnv(): void
    {
        $dotenv = Dotenv::createImmutable(PROJECT_ROOT);
        $dotenv->load();
    }

    private function registerBindings(): void
    {
    }

    private function registerDatabase(): void
    {
        $db = new Database(
            $_ENV['DB_HOST'],
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD'],
            $_ENV['DB_DATABASE'],
            (int) ($_ENV['DB_PORT'] ?? 3306)
        );

        Model::setConnection($db->conn());
        $this->container->bind(Database::class, fn () => $db);
    }

    private function registerRoutesFromContexts(string $pattern): void
    {
        foreach (glob($pattern) as $file) {
            $routes = require $file;

            foreach ($routes as [$method, $path, $action]) {
                [$class, $methodName] = $action;

                $this->router->add($method, $path, function (BaseRequest $baseRequest) use ($class, $methodName) {
                    $controller = $this->container->get($class);
                    $refMethod = new \ReflectionMethod($controller, $methodName);
                    $args = [];

                    foreach ($refMethod->getParameters() as $param) {
                        $paramType = $param->getType();

                        if ($paramType && !$paramType->isBuiltin()) {
                            $paramClass = $paramType->getName();

                            if (is_subclass_of($paramClass, BaseRequest::class)) {
                                $args[] = new $paramClass(
                                    $baseRequest->query,
                                    $baseRequest->body,
                                    $baseRequest->params
                                );
                                continue;
                            }

                            $args[] = $this->container->get($paramClass);
                        } else {
                            $paramName = $param->getName();
                            $value = $baseRequest->params[$paramName] ?? null;

                            if ($paramType) {
                                $typeName = $paramType->getName();
                                settype($value, $typeName);
                            }

                            $args[] = $value;
                        }
                    }

                    return $controller->{$methodName}(...$args);
                });
            }
        }
    }

    public function run(): void
    {
        try {
            $this->router->run();
        } catch (InvalidArgumentException $e) {
            http_response_code(422);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } catch (\JsonException $e) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], JSON_THROW_ON_ERROR);
        }
    }
}
