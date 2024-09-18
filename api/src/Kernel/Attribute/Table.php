<?php

declare(strict_types= 1);

namespace Camagru\Kernel\Attribute;

#[\Attribute]
class Table
{
    public function __construct(
        private readonly string $name,
        private readonly string $primaryKey = '',
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }
}
