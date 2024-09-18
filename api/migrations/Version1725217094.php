<?php

declare(strict_types=1);

use Camagru\Kernel\Contract\MigrationInterface;

class Version1725217094 implements MigrationInterface
{
    public function up(): string
    {
        return <<<SQL
            CREATE TABLE comment(
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                gallery_id INT NOT NULL,
                message TEXT NOT NULL,
                created_at DATETIME DEFAULT NOW(),
                CONSTRAINT fk_comment_user_id FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
                CONSTRAINT fk_comment_gallery_id FOREIGN KEY (gallery_id) REFERENCES gallery(id) ON DELETE CASCADE 
            );
        SQL;
    }

    public function down(): string
    {
        return <<<SQL
            DROP TABLE comment;
        SQL;
    }
}