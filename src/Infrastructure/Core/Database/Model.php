<?php

declare(strict_types=1);

namespace VM\Infrastructure\Core\Database;

abstract class Model
{
    protected static \mysqli $conn;

    protected static string $table;
    protected static string $primaryKey = 'id';

    protected static array $columns;

    public int $id;

    public static function setConnection(\mysqli $conn): void
    {
        static::$conn = $conn;
    }

    protected static function qb(): QueryBuilder
    {
        return new QueryBuilder(static::$conn);
    }

    public static function all(): array
    {
        $rows = static::qb()->table(static::$table)->get();

        return array_map(static fn ($r) => static::mapRow($r), $rows);
    }

    public static function find(int $id): ?array
    {
        $row = static::qb()
            ->table(static::$table)
            ->where(static::$primaryKey, '=', $id)
            ->first();

        return $row ? static::mapRow($row) : null;
    }

    public static function where(array $conditions, ?int $limit = 25, ?int $offset = 0): array
    {
        $qb = static::qb()->table(static::$table);
        foreach ($conditions as $column => $value) {
            $qb->where($column, '=', $value);
        }

        $qb->limit($limit, $offset);

        $rows = $qb->get();

        return array_map(static fn ($r) => static::mapRow($r), $rows);
    }

    public static function orWhere(array $conditions, ?int $limit = 25, ?int $offset = 0): array
    {
        $qb = static::qb()->table(static::$table);
        foreach ($conditions as $column => $value) {
            $qb->orWhere($column, '=', $value);
        }

        $qb->limit($limit, $offset);

        $rows = $qb->get();

        return array_map(static fn ($r) => static::mapRow($r), $rows);
    }

    public static function create(array $data): array
    {
        $id = static::qb()->table(static::$table)->insert($data);
        $data[static::$primaryKey] = $id;

        return static::mapRow($data);
    }

    public function update(array $data): bool
    {
        return static::qb()->table(static::$table)
            ->where(static::$primaryKey, '=', $this->id)
            ->update($data);
    }

    public function delete(): bool
    {
        return static::qb()->table(static::$table)
            ->where(static::$primaryKey, '=', $this->id)
            ->delete();
    }

    protected static function mapRow(array $row): array
    {
        return array_map(static function ($v) {
            return $v;
        }, $row);
    }
}
