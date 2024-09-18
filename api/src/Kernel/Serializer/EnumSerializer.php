<?php

declare(strict_types=1);

namespace Camagru\Kernel\Serializer;

class EnumSerializer implements SerializerInterface
{
    public function support(string $type): bool
    {
        return enum_exists($type);
    }

    public function normalize(string $type, $value)
    {
        return $value->value;
    }

    public function denormalize(string $type, $value): ?\BackedEnum
    {
        /** @var $type \BackedEnum */
        return $type::tryFrom($value);
    }
}
