<?php

namespace Camagru\Kernel\Serializer;

class BooleanSerializer implements SerializerInterface
{

    public function support(string $type): bool
    {
        return $type === 'boolean' || $type === 'bool';
    }

    public function normalize(string $type, $value)
    {
        return (int)$value;
    }

    public function denormalize(string $type, $value)
    {
        return (bool)$value;
    }
}