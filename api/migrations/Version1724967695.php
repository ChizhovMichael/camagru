<?php

declare(strict_types=1);

use Camagru\Kernel\Contract\MigrationInterface;

class Version1724967695 implements MigrationInterface
{
    public function up(): string
    {
        return <<<SQL
            ALTER TABLE user ADD COLUMN recovery_token VARCHAR(16) DEFAULT NULL AFTER confirmation_token;
        SQL;
    }

    public function down(): string
    {
        return <<<SQL
            ALTER TABLE user DROP COLUMN recovery_token;
        SQL;
    }
}