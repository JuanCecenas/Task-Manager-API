<?php

require_once __DIR__ . '/bootstrap/app.php';

use App\Core\Database;

function ensureMigrationsTable(PDO $pdo): void
{
    $result = $pdo->query("SHOW TABLES LIKE 'migrations'");

    if ($result->rowCount() === 0) {
        echo "Creating migrations table...\n";

        $pdo->exec("
            CREATE TABLE migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }
}

$pdo = Database::getConnection();

ensureMigrationsTable($pdo);

$migrationFiles = glob(__DIR__ . '/database/migrations/*.php');

// get executed migrations
$executed = $pdo->query("SELECT migration FROM migrations")
    ->fetchAll(PDO::FETCH_COLUMN);

foreach ($migrationFiles as $file) {
    $name = basename($file);

    if (in_array($name, $executed)) {
        continue;
    }

    echo "Running migration: $name\n";

    $migration = require $file;

    $migration->up($pdo);

    $stmt = $pdo->prepare("INSERT INTO migrations (migration) VALUES (?)");
    $stmt->execute([$name]);
}

echo "Done.\n";