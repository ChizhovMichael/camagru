<?php

declare(strict_types=1);

use Camagru\Kernel\Contract\MigrationInterface;

class Version1725049532 implements MigrationInterface
{
    public function up(): string
    {
        return <<<SQL
            CREATE TABLE gallery(
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT DEFAULT NULL,
                file VARCHAR(255) NULL,
                created_at DATETIME,
                CONSTRAINT fk_gallery_user_id FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
            );
        SQL;
    }

    public function down(): string
    {
        return <<<SQL
            DROP TABLE gallery;
        SQL;
    }
}