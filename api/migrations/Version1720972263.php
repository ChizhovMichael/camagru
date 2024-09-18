<?php

declare(strict_types=1);

use Camagru\Kernel\Contract\MigrationInterface;

class Version1720972263 implements MigrationInterface
{
    public function up(): string
    {
        return <<<SQL
            CREATE TABLE user(
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255),
                email VARCHAR(255),
                password VARCHAR(60),
                created_at DATETIME,
                updated_at DATETIME NULL,
                UNIQUE KEY uniq_index_user_gallery (email) 
            );
        SQL;
    }

    public function down(): string
    {
        return <<<SQL
            DROP TABLE user;
        SQL;
    }
}