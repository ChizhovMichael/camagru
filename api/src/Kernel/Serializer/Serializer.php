<?php

declare(strict_types=1);

namespace Camagru\Kernel\Serializer;

use Camagru\Kernel\Exception\SerializerException;

class Serializer
{
    public static function normalize(string $type, $value)
    {
        if (null === $value) {
            return null;
        }

        try {
            return (self::find($type))->normalize($type, $value);
        } catch (SerializerException $exception) {
            return is_string($value) ? htmlspecialchars($value) : $value;
        }
    }

    public static function denormalize(string $type, $value)
    {
        if (null === $value) {
            return null;
        }

        try {
            return (self::find($type))->denormalize($type, $value);
        } catch (SerializerException $exception) {
            settype($value, $type);
            return $value;
        }
    }

    /**
     * @throws SerializerException
     */
    private static function find(string $type)
    {
        foreach (SerializerInterface::PROVIDERS as $provider) {
            $instance = new $provider();
            if ($instance->support($type)) {
                return $instance;
            }
        }

        throw new SerializerException('Could not find serializer for ' . $type);
    }
}