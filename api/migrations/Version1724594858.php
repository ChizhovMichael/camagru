<?php

declare(strict_types=1);

use Camagru\Kernel\Contract\MigrationInterface;

class Version1724594858 implements MigrationInterface
{
    public function up(): string
    {
        return <<<SQL
            ALTER TABLE user ADD COLUMN confirmation_token VARCHAR(16) DEFAULT NULL AFTER confirmed;
        SQL;
    }

    public function down(): string
    {
        return <<<SQL
            ALTER TABLE user DROP COLUMN confirmation_token;
        SQL;
    }
}