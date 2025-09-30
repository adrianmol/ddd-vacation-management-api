<?php

declare(strict_types=1);

namespace VM\Infrastructure\Facade;

abstract class AbstractFacade
{
    protected static ?array $factories = null;

    abstract protected static function createFactory(): object;

    protected function getFactory(): object
    {
        $factory = static::createFactory()::class;

        if (!isset(static::$factories[$factory]) || null === static::$factories[$factory]) {
            static::$factories[$factory] = static::createFactory();
        }

        return static::$factories[$factory];
    }
}
