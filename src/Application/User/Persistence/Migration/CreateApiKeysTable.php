<?php

declare(strict_types=1);

namespace VM\Application\User\Persistence\Migration;

use VM\Infrastructure\Core\Database\Migration\MigrationInterface;

class CreateApiKeysTable implements MigrationInterface
{
    public function up(\mysqli $conn): void
    {
        $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS api_keys (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            api_key CHAR(255) NOT NULL UNIQUE,
            name VARCHAR(100) NOT NULL,
            scopes JSON NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_used_at TIMESTAMP NULL,
            expires_at TIMESTAMP NULL,
            
            INDEX idx_user_id (user_id),
            INDEX idx_is_active (is_active)
            
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        SQL;

        $conn->query($sql);
    }

    public function down(\mysqli $conn): void
    {
        $conn->query('DROP TABLE IF EXISTS api_keys');
    }
}
