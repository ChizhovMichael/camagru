<?php

declare(strict_types= 1);

namespace Camagru\Kernel\Model;

use Camagru\Kernel\Exception\BadQueryBuilderException;

class Builder
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    protected function getConnection(): Connection
    {
        return $this->connection;
    }

    protected function setConnection(Connection $connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    protected static function toSnakeCase(string $string): string
    {
        return strtolower(
            preg_replace(
                '/([a-z])([A-Z])/', 
                '$1_$2', 
                ltrim($string, '!')
            )
        );
    }

    protected static function toCamelCase(string $string): string
    {
        return lcfirst(
            str_replace(
                ' ', 
                '', 
                ucwords(str_replace('_', ' ', $string))
            )
        );
    }

    protected function insert(array $data = []): array
    {
        try {
            $this->connection->validate($data);
            return $this->connection->insert($data);
        } catch (\Throwable $e) {
            throw new BadQueryBuilderException($e->getMessage());
        }
    }

    protected function update(array $data = []): array
    {
         try {
             $this->connection->validate($data);
             return $this->connection->update($data);
         } catch (\Throwable $e) {
             throw new BadQueryBuilderException($e->getMessage());
         }
    }

    protected function get(array $data = []): array
    {
         try {
             $this->connection->validate($data);
             return $this->connection->get($data);
         } catch (\Throwable $e) {
             throw new BadQueryBuilderException($e->getMessage());
         }
    }

    protected function remove(array $data = []): void
    {
        try {
            $this->connection->validate($data);
            $this->connection->delete($data);
        } catch (\Throwable $e) {
            throw new BadQueryBuilderException($e->getMessage());
        }
    }
}
