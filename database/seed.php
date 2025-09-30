<?php
declare(strict_types=1);

use VM\Infrastructure\Core\Database\Database;
use VM\Infrastructure\Core\Database\Seeder\SeederInterface;

require __DIR__ . '/../vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Connect DB
$db = new Database(
    $_ENV['DB_HOST'],
    $_ENV['DB_USERNAME'],
    $_ENV['DB_PASSWORD'],
    $_ENV['DB_DATABASE'],
    (int) ($_ENV['DB_PORT'] ?? 3306)
);

$conn = $db->conn();

// Run all seeders
foreach (glob(__DIR__ . '/../src/Application/*/Persistence/Seeder/*.php') as $file) {
    require_once $file;

    $class = basename($file, '.php');
    $namespace = "VM\\Application\\" .
        explode('/', str_replace('\\', '/', strstr($file, 'Application/')))[1] .
        "\\Persistence\\Seeder\\$class";

    if (class_exists($namespace)) {
        $seeder = new $namespace();
        if ($seeder instanceof SeederInterface) {
            echo "Seeding: $class\n";
            $seeder->run($conn);
        }
    }
}

echo "✅ Seeding complete.\n";
