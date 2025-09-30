<?php

declare(strict_types=1);

namespace VM\Infrastructure\Core\Database;

class QueryBuilder
{
    private \mysqli $conn;

    private string $table;
    private array $select = ['*'];
    private array $wheres = [];
    private array $params = [];
    private array $types = [];
    private ?int $limit = null;
    private ?int $offset = null;

    public function __construct(\mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function table(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function select(array $columns = ['*']): self
    {
        $this->select = $columns;

        return $this;
    }

    public function where(string $column, string $operator, mixed $value, ?string $boolean = 'AND'): self
    {
        $this->wheres[] = [
            'boolean' => $boolean,
            'condition' => "$column $operator ?",
        ];
        $this->params[] = $value;
        $this->types[] = is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');

        return $this;
    }

    public function orWhere(string $column, string $operator, mixed $value): self
    {
        $this->wheres[] = [
            'boolean' => 'OR',
            'condition' => "$column $operator ?",
        ];
        $this->params[] = $value;
        $this->types[] = is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');

        return $this;
    }

    public function limit(int $limit, int $offset = 0): self
    {
        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }

    public function get(): array
    {
        $sql = 'SELECT '.implode(',', $this->select)." FROM {$this->table}";
        if ($this->wheres) {
            $clauses = [];
            foreach ($this->wheres as $i => $where) {
                if (0 === $i) {
                    $clauses[] = $where['condition'];
                } else {
                    $clauses[] = $where['boolean'].' '.$where['condition'];
                }
            }
            $sql .= ' WHERE '.implode(' ', $clauses);
        }
        if (null !== $this->limit) {
            $sql .= " LIMIT {$this->limit} OFFSET {$this->offset}";
        }

        $stmt = $this->prepareAndBind($sql);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function first(): ?array
    {
        $this->limit(1);
        $rows = $this->get();

        return $rows[0] ?? null;
    }

    public function insert(array $data): int
    {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        $this->params = array_values($data);
        $this->types = array_map(fn ($v) => is_int($v) ? 'i' : (is_float($v) ? 'd' : 's'), $this->params);

        $stmt = $this->prepareAndBind($sql);
        $stmt->execute();

        return $this->conn->insert_id;
    }

    public function update(array $data): bool
    {
        $set = implode(',', array_map(fn ($col) => "$col = ?", array_keys($data)));
        $sql = "UPDATE {$this->table} SET $set";

        if ($this->wheres) {
            $clauses = [];
            foreach ($this->wheres as $i => $where) {
                if (0 === $i) {
                    $clauses[] = $where['condition'];
                } else {
                    $clauses[] = $where['boolean'].' '.$where['condition'];
                }
            }
            $sql .= ' WHERE '.implode(' ', $clauses);
        }

        $this->params = [...array_values($data), ...$this->params];
        $this->types = [...array_map(static fn ($v) => is_int($v) ? 'i' : (is_float($v) ? 'd' : 's'), array_values($data)), ...$this->types];

        return $this->prepareAndBind($sql)->execute();
    }

    public function delete(): bool
    {
        $sql = "DELETE FROM {$this->table}";
        if ($this->wheres) {
            $sql .= ' WHERE '.implode(' AND ', $this->wheres);
        }

        return $this->prepareAndBind($sql)->execute();
    }

    private function prepareAndBind(string $sql): \mysqli_stmt
    {
        $stmt = $this->conn->prepare($sql);

        if ($this->params) {
            $stmt->bind_param(implode('', $this->types), ...$this->params);
        }

        return $stmt;
    }
}
