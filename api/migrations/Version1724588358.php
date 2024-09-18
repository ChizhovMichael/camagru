<?php

declare(strict_types=1);

use Camagru\Kernel\Contract\MigrationInterface;

class Version1724588358 implements MigrationInterface
{
    public function up(): string
    {
        return <<<SQL
            ALTER TABLE user ADD COLUMN confirmed BOOLEAN DEFAULT 0 NOT NULL AFTER password;
        SQL;
    }

    public function down(): string
    {
        return <<<SQL
            ALTER TABLE user DROP COLUMN confirmed;
        SQL;
    }
}