<?php

declare(strict_types=1);

namespace VM\Infrastructure\Transfer;

abstract class AbstractTransfer
{
    protected static array $protectedKeys = [];

    protected static array $propertyTypes = [];

    public function __construct(?array $data = [])
    {
        $this->initializeNullableProperties();
        $this->fromArray($data);
    }

    public function fromArray(?array $data): static
    {
        if (is_null($data)) {
            return $this;
        }

        foreach ($data as $key => $value) {
            $prop = $this->toCamel($key);

            if (!property_exists($this, $prop)) {
                continue;
            }

            if (isset(static::$propertyTypes[$prop]) && !is_null($value)) {
                $class = static::$propertyTypes[$prop];
                $this->$prop = is_array($value)
                    ? new $class($value)
                    : $value;
                continue;
            }

            $this->$prop = $value;
        }

        return $this;
    }

    public function toArray(bool $snakeCase = false, bool $includeNulls = true): array
    {
        $array = [];

        foreach (get_object_vars($this) as $key => $value) {
            if (in_array($key, static::$protectedKeys, true)) {
                continue;
            }

            if (null === $value && !$includeNulls) {
                continue;
            }

            if ($value instanceof \DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            }

            if ($value instanceof self) {
                $value = $value->toArray($snakeCase, $includeNulls);
            }

            $array[$snakeCase ? $this->toSnake($key) : $this->toCamel($key)] = $value;
        }

        return $array;
    }

    private function toCamel(string $string): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $string))));
    }

    private function toSnake(string $string): string
    {
        return strtolower(preg_replace('/[A-Z]/', '_$0', lcfirst($string)));
    }

    private function initializeNullableProperties(): void
    {
        $ref = new \ReflectionClass($this);
        foreach ($ref->getProperties() as $prop) {
            if (in_array($prop->getName(), ['protectedKeys', 'propertyTypes'], true)) {
                continue;
            }

            $type = $prop->getType();
            $name = $prop->getName();
            if (!isset($this->$name) && $type && $type->allowsNull()) {
                $this->$name = null;
            }
        }
    }
}
