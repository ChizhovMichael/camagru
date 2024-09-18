<?php

declare(strict_types=1);

namespace Camagru\Kernel\Contract;

interface MigrationInterface
{
    public function up(): string;

    public function down(): string;
}