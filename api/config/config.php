<?php

use Camagru\Kernel\Dotenv;

return [
    /** Path for main dir  */
    'root_path' => dirname(__DIR__),
    'var_path' => dirname(__DIR__).'/var',

    /** Path for placing views */
    'views' => [
        'path' => dirname(__DIR__).'/resources/views',
        'suffix' => 'blade.php',
        'extension' => 'php',
    ],

    /** Default driver for database connection */
    'default_driver' => 'mariadb',

    /** Mysql connection settings */
    'mysql' => [
        'driver'    => 'mysql',
        'server'    => Dotenv::get('DATABASE_SERVER'),
        'database'  => Dotenv::get('DATABASE_NAME'),
        'username'  => Dotenv::get('DATABASE_USER'),
        'password'  => Dotenv::get('DATABASE_PASSWORD'),
        'charset'   => Dotenv::get('DATABASE_CHARSET'),
    ],

    /** MariaDb connection settings */
    'mariadb' => [
        'driver'    => 'mysql',
        'server'    => Dotenv::get('DATABASE_SERVER'),
        'database'  => Dotenv::get('DATABASE_NAME'),
        'username'  => Dotenv::get('DATABASE_USER'),
        'password'  => Dotenv::get('DATABASE_PASSWORD'),
        'charset'   => Dotenv::get('DATABASE_CHARSET'),
    ],
];
