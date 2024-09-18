<?php

declare(strict_types=1);

namespace Camagru\Kernel\Serializer;

interface SerializerInterface
{
    public function support(string $type): bool;

    public function normalize(string $type, $value);

    public function denormalize(string $type, $value);

    public const PROVIDERS = [
        DateTimeSerializer::class,
        EnumSerializer::class,
        BooleanSerializer::class,
        ArraySerializer::class,
    ];
}