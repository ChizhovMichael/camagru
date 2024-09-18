<?php

declare(strict_types=1);

namespace Camagru\Kernel;

class Dotenv
{
    public function load($path): void
    {
        $lines = file($path . '/.env');
        if (false === $lines) {
            throw new \RuntimeException('Env file not found.');
        }

        foreach ($lines as $line) {
            if (!trim($line)) {
                continue;
            }
            try {
                [$key, $value] = explode('=', $line, 2);
            } catch (\Exception $e) {
                continue;
            }
            $key = trim($key);
            $value = trim($value);

            putenv(sprintf('%s=%s', $key, $value));
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        return isset($_ENV[$key]) ? $_ENV[$key] : $default;
    }
}
