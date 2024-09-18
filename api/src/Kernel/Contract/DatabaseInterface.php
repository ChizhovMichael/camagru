<?php

declare(strict_types=1);

namespace Camagru\Kernel\Contract;

interface DatabaseInterface
{
    public const TABLE = 'table';
    public const PRIMARY_KEY = 'primaryKey';
    public const WHERE = 'where';
    public const COLUMNS = 'columns';
    public const VALUES = 'values';
    public const ORDER = 'order';

    public const AVAILABLE_KEYS = [
        self::TABLE,
        self::PRIMARY_KEY,
        self::WHERE,
        self::COLUMNS,
        self::VALUES,
        self::ORDER,
    ];

    public const OPERATORS = [
        'in' => 'IN',
        'eq' => '=',
        'gte' => '>=',
        'gt' => '>',
        'lt' => '<',
        'lte' => '<=',
    ];
}
