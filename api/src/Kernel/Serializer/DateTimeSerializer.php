<?php

declare(strict_types=1);

namespace Camagru\Kernel\Serializer;

class DateTimeSerializer implements SerializerInterface
{
    public function support(string $type): bool
    {
        return str_contains($type, 'Date');
    }

    public function normalize(string $type, $value)
    {
        global $config;

        $driver = $config['default_driver'];
        $format = match ($driver) {
            'mysql' => \DateTimeInterface::RFC3339_EXTENDED,
            'mariadb' => 'Y-m-d H:i:s',
        };
        return $value->format($format);
    }

    public function denormalize(string $type, $value)
    {
        if ($type === 'DateTimeInterface') {
            return new \DateTime($value);
        }

        return new ($type)($value);
    }
}
