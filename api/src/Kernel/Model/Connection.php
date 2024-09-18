<?php

declare(strict_types= 1);

namespace Camagru\Kernel\Model;

abstract class Connection
{
    protected static Connection|null $instance = null;

    protected static $provider = null;

    protected function __construct(...$args)
    {
        static::__construct(...$args);
    }

    abstract public function get($bindings = []): array;

    abstract public function insert($bindings = []): array;

    abstract public function update($bindings = []): array;

    abstract public function delete($bindings = []): void;

    abstract public function validate($bindings = []): void;

    abstract protected function reconnect(): void;

    abstract public static function create(): static;

    public static function build(...$args): static
    {
        if (!self::$instance) {
            self::$instance = new static(...$args);
        }

        return self::$instance;
    }

    public function connect(): self
    {
        if (is_null(self::$provider)) {
            $this->reconnect();
        }

        return $this;
    }

    public function provider()
    {
        return self::$provider;
    }

    public function __clone()
    {
        throw new \Exception("Can't clone a Connection instance");
    }

    public function __wakeup()
    {
        static::reconnect();
    }
}
