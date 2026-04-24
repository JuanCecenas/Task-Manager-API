<?php


return new class {
    public function up(\PDO $pdo): void
    {
        $pdo->exec("
            CREATE TABLE tasks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                status VARCHAR(50) DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function down(PDO $pdo)
    {
        $pdo->exec("DROP TABLE tasks");
    }
};