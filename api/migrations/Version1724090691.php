<?php

declare(strict_types=1);

use Camagru\Kernel\Contract\MigrationInterface;

class Version1724090691 implements MigrationInterface
{
    public function up(): string
    {
        return <<<SQL
            CREATE TABLE session(
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                refresh_token VARCHAR(64) NULL,
                created_at DATETIME,
                CONSTRAINT fk_session_user_id FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
            );
        SQL;
    }

    public function down(): string
    {
        return <<<SQL
            DROP TABLE session;
        SQL;
    }
}