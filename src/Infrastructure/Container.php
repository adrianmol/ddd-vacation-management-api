<?php

declare(strict_types=1);

namespace VM\Infrastructure;

class Container
{
    /** @var array<class-string, callable|class-string> */
    private array $bindings = [];

    /** @var array<class-string, object> */
    private array $instances = [];

    /**
     * Bind an abstraction to a concrete (class-string) or a factory (callable).
     * e.g. bind(UserRepositoryInterface::class, UserRepository::class).
     */
    public function bind(string $abstract, callable|string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function singleton(string $abstract, callable|string $concrete): void
    {
        $this->bindings[$abstract] = static function ($c) use ($concrete) {
            static $instance = null;

            return $instance ??= is_string($concrete)
                ? $c->autowire($concrete)
                : $concrete($c);
        };
    }

    public function get(string $id): object
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (isset($this->bindings[$id])) {
            $concrete = $this->bindings[$id];
            $object = is_string($concrete) ? $this->autowire($concrete) : $concrete($this);

            return $this->instances[$id] = $object;
        }

        $object = $this->autowire($id);

        return $this->instances[$id] = $object;
    }

    /**
     * @throws \ReflectionException
     */
    private function autowire(string $class): object
    {
        if (!class_exists($class)) {
            throw new \RuntimeException("Class not found: $class");
        }

        $ref = new \ReflectionClass($class);
        $ctor = $ref->getConstructor();
        if (!$ctor) {
            return new $class();
        }

        $deps = [];
        foreach ($ctor->getParameters() as $p) {
            $t = $p->getType();
            if ($t && !$t->isBuiltin()) {
                $deps[] = $this->get($t->getName());
            } elseif ($p->isDefaultValueAvailable()) {
                $deps[] = $p->getDefaultValue();
            } else {
                throw new \RuntimeException("Unresolvable dependency: {$p->getName()} for $class");
            }
        }

        return $ref->newInstanceArgs($deps);
    }
}
