<?php

declare(strict_types=1);

use Camagru\Kernel\Contract\MigrationInterface;

class Version1724602830 implements MigrationInterface
{
    public function up(): string
    {
        return <<<SQL
            CREATE TABLE log(
                level ENUM('info', 'error') DEFAULT 'info' NOT NULL,
                message TEXT NOT NULL,
                context JSON NOT NULL
            );
        SQL;
    }

    public function down(): string
    {
        return <<<SQL
            DROP TABLE log;
        SQL;
    }
}