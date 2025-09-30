<?php

namespace VM\Infrastructure\Core\Database\Seeder;

use mysqli;

interface SeederInterface
{
    public function run(mysqli $conn): void;
}