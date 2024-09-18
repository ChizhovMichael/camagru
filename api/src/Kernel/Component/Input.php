<?php

declare(strict_types= 1);

namespace Camagru\Kernel\Component;

class Input
{
    public function __construct(
        protected array $parameters = []
    ) {
        $this->parameters = $parameters;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return \array_key_exists($key, $this->parameters) 
            ? $this->parameters[$key] 
            : $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->parameters[$key] = $value;
    }

    /**
     * Returns true if the parameter is defined.
     */
    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->parameters);
    }

    public function all()
    {
        return $this->parameters;
    }
}
