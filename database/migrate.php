<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use VM\Infrastructure\Core\Database\Database;
use VM\Infrastructure\Core\Database\Migration\Migrator;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$db = new Database(
    $_ENV['DB_HOST'],
    $_ENV['DB_USERNAME'],
    $_ENV['DB_PASSWORD'],
    $_ENV['DB_DATABASE'],
    (int)($_ENV['DB_PORT'] ?? 3306)
);

$migrator = new Migrator($db->conn(), $_ENV['DB_DATABASE']);
$migrator->run(__DIR__ . '/../src/Application');