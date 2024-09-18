<?php

declare(strict_types=1);

use Camagru\Kernel\Contract\MigrationInterface;

class Version1725141503 implements MigrationInterface
{
    public function up(): string
    {
        return <<<SQL
            CREATE TABLE relation(
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                gallery_id INT NOT NULL,
                created_at DATETIME,
                CONSTRAINT fk_relation_user_id FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
                CONSTRAINT fk_relation_gallery_id FOREIGN KEY (gallery_id) REFERENCES gallery(id) ON DELETE CASCADE, 
                UNIQUE KEY uniq_index_user_gallery (user_id, gallery_id) 
            );
        SQL;
    }

    public function down(): string
    {
        return <<<SQL
            DROP TABLE relation;
        SQL;
    }
}