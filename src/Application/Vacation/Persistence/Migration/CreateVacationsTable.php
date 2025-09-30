<?php

declare(strict_types=1);

namespace VM\Application\Vacation\Persistence\Migration;

use VM\Infrastructure\Core\Database\Migration\MigrationInterface;

class CreateVacationsTable implements MigrationInterface
{
    public function up(\mysqli $conn): void
    {
        $sql = <<<SQL
            CREATE TABLE IF NOT EXISTS vacations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                employee_id INT NOT NULL,
                manager_id INT DEFAULT NULL,
                start_date DATE NOT NULL,
                end_date DATE NOT NULL,
                reason TEXT,
                status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
                INDEX idx_employee (employee_id),
                INDEX idx_manager (manager_id),
                INDEX idx_status (status)
            
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        SQL;

        $conn->query($sql);
    }

    public function down(\mysqli $conn): void
    {
        $conn->query('DROP TABLE IF EXISTS vacations');
    }
}
