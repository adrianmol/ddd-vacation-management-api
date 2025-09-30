<?php

declare(strict_types=1);

namespace VM\Infrastructure\Core\Database;

class Database
{
    private \mysqli $conn;

    public function __construct(
        string $host,
        string $user,
        string $password,
        string $dbname,
        int $port = 3306,
    ) {
        try {
            $this->conn = new \mysqli($host, $user, $password, $dbname, $port);
            $this->conn->set_charset('utf8mb4');
        } catch (\mysqli_sql_exception $e) {
            if (str_contains($e->getMessage(), 'Unknown database')) {
                echo "⚠️ Database '{$dbname}' does not exist. Creating...\n";

                $tmp = new \mysqli($host, $user, $password, '', $port);
                $tmp->query("CREATE DATABASE `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $tmp->close();

                echo "✅ Database '{$dbname}' created.\n";

                $this->conn = new \mysqli($host, $user, $password, $dbname, $port);
                $this->conn->set_charset('utf8mb4');
            } else {
                throw new \RuntimeException('DB connection failed: '.$e->getMessage(), 0, $e);
            }
        } catch (\RuntimeException $e) {
            throw new \RuntimeException('DB connection failed: '.$e->getMessage(), 0, $e);
        }
    }

    public function conn(): \mysqli
    {
        return $this->conn;
    }
}
