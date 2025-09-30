<?php

declare(strict_types=1);

namespace VM\Infrastructure\Core\Database\Migration;

class Migrator
{
    private \mysqli $conn;
    private string $dbname;

    public function __construct(\mysqli $conn, string $dbname)
    {
        $this->conn = $conn;
        $this->dbname = $dbname;
        $this->ensureMigrationsTable();
    }

    public function run(string $basePath): void
    {
        foreach (glob($basePath.'/*/Persistence/Migration/*.php') as $file) {
            $this->runMigrationFile($file);
        }
    }

    private function runMigrationFile(string $file): void
    {
        require_once $file;

        $class = $this->getClassFromFile($file);
        if (!$class || !class_exists($class)) {
            return;
        }

        $migrationName = basename($file);

        if ($this->alreadyRun($migrationName)) {
            return;
        }

        $migration = new $class();
        if (!$migration instanceof MigrationInterface) {
            throw new \RuntimeException("$class must implement MigrationInterface");
        }

        echo "Running: $migrationName\n";
        $migration->up($this->conn);
        $this->markAsRun($migrationName);
    }

    private function alreadyRun(string $migrationName): bool
    {
        $stmt = $this->conn->prepare('SELECT COUNT(*) FROM migrations WHERE name = ?');
        $stmt->bind_param('s', $migrationName);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        return $count > 0;
    }

    private function markAsRun(string $migrationName): void
    {
        $stmt = $this->conn->prepare('INSERT INTO migrations (name) VALUES (?)');
        $stmt->bind_param('s', $migrationName);
        $stmt->execute();
    }

    private function ensureMigrationsTable(): void
    {
        $this->conn->query('
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) UNIQUE NOT NULL,
                run_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ');
    }

    private function getClassFromFile(string $file): ?string
    {
        $contents = file_get_contents($file);
        if (preg_match('/namespace\s+(.+?);/s', $contents, $nsMatch)
            && preg_match('/class\s+(\w+)/s', $contents, $classMatch)) {
            return $nsMatch[1].'\\'.$classMatch[1];
        }

        return null;
    }
}
