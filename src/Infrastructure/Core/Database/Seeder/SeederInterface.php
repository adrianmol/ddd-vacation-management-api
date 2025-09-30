<?php

declare(strict_types=1);

namespace VM\Infrastructure\Core\Database\Seeder;

interface SeederInterface
{
    public function run(\mysqli $conn): void;
}
