<?php

declare(strict_types=1);

use Camagru\Kernel\Contract\MigrationInterface;

class Version1724501336 implements MigrationInterface
{
    public function up(): string
    {
        return <<<SQL
            CREATE TABLE notification(
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                destination VARCHAR(64) NOT NULL,
                subject VARCHAR(255) NOT NULL,
                message LONGTEXT NOT NULL,
                headers JSON NOT NULL,
                created_at DATETIME,
                CONSTRAINT fk_notification_user_id FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
            );
        SQL;
    }

    public function down(): string
    {
        return <<<SQL
            DROP TABLE notification;
        SQL;
    }
}