<?php

declare(strict_types=1);

use Camagru\Kernel\Contract\MigrationInterface;

class Version1725275272 implements MigrationInterface
{
    public function up(): string
    {
        return <<<SQL
            ALTER TABLE user ADD COLUMN send_comment_notification BOOLEAN DEFAULT 1 NOT NULL AFTER recovery_token;
        SQL;
    }

    public function down(): string
    {
        return <<<SQL
            ALTER TABLE user DROP COLUMN send_comment_notification;
        SQL;
    }
}