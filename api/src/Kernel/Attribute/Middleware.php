<?php

declare(strict_types=1);

namespace Camagru\Kernel\Attribute;

use Attribute;

#[\Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
class Middleware
{
    public function __construct(
        private readonly string $class,
    ) {
    }

    public function getClass(): string
    {
        return $this->class;
    }
}
