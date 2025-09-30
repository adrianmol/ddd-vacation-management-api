<?php

declare(strict_types=1);

namespace VM\Infrastructure\Core\Database\Migration;

interface MigrationInterface
{
    public function up(\mysqli $conn): void;

    public function down(\mysqli $conn): void;
}
