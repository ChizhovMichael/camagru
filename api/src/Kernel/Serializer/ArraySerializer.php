<?php

declare(strict_types=1);

namespace Camagru\Kernel\Serializer;

class ArraySerializer implements SerializerInterface
{

    public function support(string $type): bool
    {
        return $type === 'json' || $type === 'array';
    }

    public function normalize(string $type, $value)
    {
        return json_encode($value, JSON_THROW_ON_ERROR);
    }

    public function denormalize(string $type, $value)
    {
        try {
            return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $exception) {
            return [];
        }
    }
}