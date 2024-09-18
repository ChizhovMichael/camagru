<?php

declare(strict_types=1);

namespace Camagru\Kernel\Model\Connection;

use Camagru\Kernel\Contract\DatabaseInterface;
use Camagru\Kernel\Model\Connection;

class Database extends Connection implements DatabaseInterface
{
    public function __construct(
        protected string $username,
        protected string $password,
        protected string $driver,
        protected string $server,
        protected string $database,
        protected string $charset,
    ) {
    }

    public static function create(): static
    {
        global $config;
        $driver = $config['default_driver'];
        return parent::build(...$config[$driver]);
    }

    /**
     * @throws \Exception
     */
    public function insert($bindings = []): array
    {
        $connect = $this->connect()->provider();
        if (!$connect) {
            throw new \Exception('Connection not found.');
        }

        $pk = $this->primaryKey($bindings);
        $values = $this->values($bindings);
        $table = $this->table($bindings);
        if (!$table) {
            throw new \Exception('Unavailable empty param table.');
        }

        $keys = array_keys($values);
        $cols = array_map(function($key) {
            return sprintf('`%s`', $key);
        }, $keys);
        $placeholders = array_map(function($key) {
            return sprintf(':%s', $key);
        }, $keys);
        $params = array_combine($placeholders, $values);
        $query = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $this->table($bindings),
            implode(',', $cols),
            implode(',', $placeholders)
        );

        $stmt = $connect->prepare($query);
        $stmt->execute($params);
        $last = $connect->lastInsertId();
        $stmt->closeCursor();
        $stmt = null;
        return [...$values, ...($pk ? [$pk => $last] : [])];
    }

    /**
     * @throws \Exception
     */
    public function update($bindings = []): array
    {
        $connect = $this->connect()->provider();
        if (!$connect) {
            throw new \Exception('Connection not found.');
        }

        $table = $this->table($bindings);
        $values = $this->values($bindings);
        if (!$table) {
            throw new \Exception('Unavailable empty param table.');
        }

        $keys = array_keys($values);
        $placeholders = array_map(function($key) {
            return sprintf(':new_%s', $key);
        }, $keys);
        $cols = array_combine($keys, $placeholders);
        list($conditions, $params) = $this->parseWhere($bindings);

        $query = sprintf(
            'UPDATE `%s` SET %s WHERE %s',
            $table,
            implode(', ', array_map(
                static fn ($key, $value) => "$key = $value",
                array_keys($cols),
                $cols,
            )),
            implode(' AND ', $conditions),
        );
        $stmt = $connect->prepare($query);
        $stmt->execute(array_combine($placeholders, $values) + $params);
        $stmt->closeCursor();
        $stmt = null;

        return $values;
    }

    /**
     * @throws \Exception
     */
    public function get($bindings = []): array
    {
        $connect = $this->connect()->provider();
        if (!$connect) {
            throw new \Exception('Connection not found.');
        }

        $columns = $this->columns($bindings);
        $table = $this->table($bindings);
        $order = $this->order($bindings);
        if (!$table) {
            throw new \Exception('Unavailable empty param table.');
        }

        list($conditions, $params) = $this->parseWhere($bindings);

        $sql = 'SELECT %s FROM `%s`';
        $args = [implode(', ', $columns), $table];

        if (count($conditions)) {
            $sql .= ' WHERE %s';
            $args[] = implode(' AND ', $conditions);
        }
        if (count($order)) {
            $sql .= ' ORDER BY %s';
            $args[] = implode(', ', array_map(
                static fn ($key, $value) => $key . ' ' . strtoupper($value),
                array_keys($order),
                $order
            ));
        }

        $query = vsprintf($sql, $args);
        $stmt = $connect->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll();
        $stmt->closeCursor();
        $stmt = null;

        return $result;
    }

    /**
     * @throws \Exception
     */
    public function delete($bindings = []): void
    {
        $connect = $this->connect()->provider();
        if (!$connect) {
            throw new \Exception('Connection not found.');
        }

        $table = $this->table($bindings);
        if (!$table) {
            throw new \Exception('Unavailable empty param table.');
        }

        list($conditions, $params) = $this->parseWhere($bindings);

        if (!count($conditions)) {
            throw new \Exception('Unavailable empty param where.');
        }

        $sql = 'DELETE FROM `%s` WHERE %s';
        $query = sprintf(
            $sql,
            $table,
            implode(' AND ', $conditions),
        );
        $stmt = $connect->prepare($query);
        $stmt->execute($params);
        $stmt->closeCursor();
        $stmt = null;
    }

    protected function reconnect(): void
    {
        if (self::$provider === null) {
            self::$provider = new \PDO(
                sprintf(
                    "%s:host=%s;dbname=%s;charset=%s",
                    $this->driver,
                    $this->server,
                    $this->database,
                    $this->charset,
                ), 
                $this->username, 
                $this->password, 
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }
    }

    /**
     * @throws \Exception
     */
    public function validate($bindings = []): void
    {
        foreach (array_keys($bindings) as $key) {
            if (!in_array($key, DatabaseInterface::AVAILABLE_KEYS)) {
                throw new \Exception(
                    "Error validating data structure. Not find {$key} key."
                );
            }
        }
    }

    protected function table(array $data): string
    {
        return $data[DatabaseInterface::TABLE];
    }

    protected function primaryKey(array $data): string 
    {
        return $data[DatabaseInterface::PRIMARY_KEY];
    }

    protected function columns(array $data): array
    {
        return $data[DatabaseInterface::COLUMNS] ?? [];
    }

    protected function where(array $data): array
    {
        return $data[DatabaseInterface::WHERE] ?? [];
    }

    protected function values(array $data): array
    {
        return $data[DatabaseInterface::VALUES] ?? [];
    }

    private function parseWhere(array $bindings): array
    {
        $where = array_filter($this->where($bindings));

        $conditions = [];
        $params = [];
        foreach ($where as $key => $value) {
            try {
                list($field, $operatorKey) = explode(':', $key);
            } catch (\Throwable $exception) {
                throw new \InvalidArgumentException("Undefined operator or column name: $key");
            }
            if (!array_key_exists($operatorKey, static::OPERATORS)) {
                throw new \InvalidArgumentException("Unknown operator: $operatorKey");
            }
            $operator = static::OPERATORS[$operatorKey];
            if ($operator === 'IN') {
                if (!$value) {
                    continue;
                }
                $param = implode(', ', (array)$value);
                $conditions[] = "{$field} {$operator} ({$param})";
            } else {
                $conditions[] = "{$field} {$operator} :{$field}";
                $params[":{$field}"] = $value;
            }
        }

        return [$conditions, $params];
    }

    protected function order(array $data): array
    {
        return $data[DatabaseInterface::ORDER] ?? [];
    }
}
