<?php

declare(strict_types= 1);

namespace Camagru\Kernel\Model;

use Camagru\Kernel\Attribute\Hidden;
use Camagru\Kernel\Attribute\LifecycleCallbacks;
use Camagru\Kernel\Attribute\Table;
use Camagru\Kernel\Contract\DatabaseInterface;
use Camagru\Kernel\Model\Connection\Database;
use Camagru\Kernel\Serializer\Serializer;

class Model extends Builder implements \JsonSerializable
{
    /**
     * The table associated with the model.
     *
     * @var string|null
     */
    protected ?string $table = null;

    /**
     * The primary key associated with the table.
     *
     * @var string|null
     */
    protected ?string $primaryKey = null;

    /**
     * Cache properties.
     *
     * @var array
     */
    private array $initConditions = [];

    /**
     * The callback is triggered before insert data.
     *
     * @var string|null
     */
    protected ?string $createdCallbackMethod = null;

    /**
     * The callback is triggered before update data.
     *
     * @var string|null
     */
    protected ?string $updatedCallbackMethod = null;

    /**
     * The callback is triggered before delete data.
     *
     * @var string|null
     */
    protected ?string $deletedCallbackMethod = null;

    /**
     * Exists entity.
     *
     * @var bool
     */
    protected bool $exists = false;

    public function __construct()
    {
        $database = Database::create();
        $this->setAttributes();
        $this->setConnection($database);
    }

    private function setAttributes(): void
    {
        $attributes = [];
        $reflectionClass = new \ReflectionClass(static::class);
        foreach ($reflectionClass->getAttributes() as $attr) {
            $attributes[$attr->getName()][] = $attr->getArguments();
        }

        if (isset($attributes[Table::class])) {
            $table = end($attributes[Table::class]);
            $this->primaryKey ??= $table['primaryKey'] ?? '';
            $this->table ??= $table['name'] ?? '';
        }

        if (isset($attributes[LifecycleCallbacks::class])) {
            $callbacks = end($attributes[LifecycleCallbacks::class]);
            foreach ($callbacks as $callback) {
                $this->{$callback} ??= $callback;
            }
        }

        if (null === $this->table) {
            throw new \RuntimeException(
                'table attribute not found for '.static::class
            );
        }
    }

    public function save(): void
    {
        // execute create callback
        if ($this->createdCallbackMethod && !$this->exists) {
            call_user_func([
                $this, 
                $this->createdCallbackMethod
            ]);
        }

        // execute update callback
        if ($this->updatedCallbackMethod && $this->exists) {
            call_user_func([
                $this, 
                $this->updatedCallbackMethod
            ]);
        }

        if ($this->exists) {
            $property = $this->update(
                [
                    DatabaseInterface::TABLE => $this->table,
                    DatabaseInterface::WHERE => $this->initConditions,
                    DatabaseInterface::VALUES => $this->getProperties(),
                ]
            );
        } else {
            $property = $this->insert(
                [
                    DatabaseInterface::TABLE => $this->table,
                    DatabaseInterface::PRIMARY_KEY => $this->primaryKey,
                    DatabaseInterface::VALUES => $this->getProperties(),
                ]
            );
        }

        self::fillEntity($this, $property);
        self::cacheEntityCondition($this, $property);
        $this->exists = true;
    }

    public function delete()
    {
        if (!$this->exists) {
            return;
        }

        $this->remove(
            [
                DatabaseInterface::TABLE => $this->table,
                DatabaseInterface::WHERE => $this->initConditions,
            ]
        );

        // execute delete callback
        if ($this->deletedCallbackMethod) {
            call_user_func([
                $this,
                $this->deletedCallbackMethod
            ]);
        }

        return null;
    }

    public static function find(int $id): static|null
    {
        return self::findOneBy(['id:in' => $id]);
    }

    public static function findOneBy(array $conditions = [], array $order = []): static|null
    {
        $entity = new static();
        $property = current(
            $entity->get(
                [
                    DatabaseInterface::TABLE => $entity->table,
                    DatabaseInterface::COLUMNS => ['*'],
                    DatabaseInterface::WHERE => $conditions,
                    DatabaseInterface::ORDER => $order,
                ]
            )
        );
        if (!$property) {
            return null;
        }
        self::fillEntity($entity, $property);
        self::cacheEntityCondition($entity, $property);
        $entity->exists = true;
        return $entity;
    }

    public static function findBy(array $conditions = [], array $order = []): array
    {
        $builder = new static();
        $properties = $builder->get(
            [
                DatabaseInterface::TABLE => $builder->table,
                DatabaseInterface::COLUMNS => ['*'],
                DatabaseInterface::WHERE => $conditions,
                DatabaseInterface::ORDER => $order,
            ]
        );

        unset($builder);

        $entities = [];
        foreach ($properties as $property) {
            $entity = new static();
            self::fillEntity($entity, $property);
            self::cacheEntityCondition($entity, $property);
            $entity->exists = true;
            $entities[] = $entity;
        }

        return $entities;
    }

    public static function findAll(array $order = []): array
    {
        return self::findBy(order: $order);
    }

    public static function destroy(int $id): void
    {
        self::destroyBy(['id:eq' => $id]);
    }

    public static function destroyBy(array $conditions = []): void
    {
        $builder = new static();
        $builder->remove(
            [
                DatabaseInterface::TABLE => $builder->table,
                DatabaseInterface::WHERE => $conditions,
            ]
        );

        unset($builder);
    }

    /**
     * @throws \ReflectionException
     */
    private static function fillEntity(Model $entity, array $properties): void
    {
        $reflectionClass = new \ReflectionClass($entity);
        foreach($properties as $name => $value) {
            $name = preg_replace('/_id$/', '', $name);
            $name = parent::toCamelCase($name);
            $property = new \ReflectionProperty($entity::class, $name);
            $type = $property->getType()->getName();

            if (is_subclass_of($type, self::class) && $value) {
                $reflectionClass
                    ->getProperty($name)
                    ->setValue($entity, $type::find($value))
                ;
            } else {
                $reflectionClass
                    ->getProperty($name)
                    ->setValue($entity, Serializer::denormalize($type, $value))
                ;
            }
        }
    }

    private static function getModelProperties(string $class): array
    {
        return array_filter(
            array: (new \ReflectionClass($class))->getProperties(),
            callback: function (\ReflectionProperty $property) {
                $excludeReflection = new \ReflectionClass(self::class);
                foreach ($excludeReflection->getProperties() as $excludeProperty) {
                    if ($excludeProperty->getName() === $property->getName()) {
                        return false;
                    }
                }
                return true;
            }
        );
    }

    private function getProperties(): array
    {
        $properties = [];
        foreach (self::getModelProperties(static::class) as $property) {
            $name = parent::toSnakeCase($property->getName());
            $type = $property->getType()->getName();
            $value = $property->getValue($this);

            if (is_subclass_of($type, self::class)) {
                $properties[$name . '_id'] = $value?->getId();
            } else {
                $properties[$name] = Serializer::normalize($type, $value);
            }
        }

        return $properties;
    }

    private static function cacheEntityCondition(Model $entity, array $properties): void
    {
        foreach ($properties as $key => $value) {
            $entity->initConditions["$key:eq"] = $value;
        }
    }

    private static function recursiveJsonSerialize($object): array
    {
        $properties = [];
        /** @var \ReflectionProperty $property */
        foreach (self::getModelProperties($object::class) as $property) {
            $name = parent::toSnakeCase($property->getName());
            $type = $property->getType()->getName();
            $attributes = array_map(
                callback: static fn (\ReflectionAttribute $attribute) => $attribute->getName(),
                array: $property->getAttributes(),
            );
            if (in_array(Hidden::class, $attributes)) {
                continue;
            }

            $value = $property->getValue($object);

            $properties[$name] = (is_subclass_of($type, self::class) && $value)
                ? self::recursiveJsonSerialize($value)
                : Serializer::normalize($type, $value);
        }

        return $properties;
    }

    public function jsonSerialize(): array
    {
        return self::recursiveJsonSerialize($this);
    }
}
