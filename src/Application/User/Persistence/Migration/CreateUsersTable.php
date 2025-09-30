<?php

declare(strict_types=1);

namespace VM\Application\User\Persistence\Migration;

use VM\Infrastructure\Core\Database\Migration\MigrationInterface;

class CreateUsersTable implements MigrationInterface
{
    public function up(\mysqli $conn): void
    {
        $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(255) NOT NULL,
            username VARCHAR(50) NOT NULL UNIQUE,
            code VARCHAR(8),
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'manager', 'employee') NOT NULL DEFAULT 'employee',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP,
            
            INDEX idx_role (role),
            INDEX idx_deleted_at (deleted_at)
        
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        SQL;

        $conn->query($sql);
    }

    public function down(\mysqli $conn): void
    {
        $conn->query('DROP TABLE IF EXISTS users');
    }
}
