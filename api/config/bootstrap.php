<?php

use Camagru\Kernel\Dotenv;

require dirname(__DIR__).'/autoload.php';

if (!class_exists(Dotenv::class)) {
    throw new LogicException('Dotenv not found.');
}

$dotenv = new Dotenv();
$path = dirname(__DIR__);
if (!file_exists($path.'/.env')) {
    $path = dirname(__DIR__, 2);
}
$dotenv->load($path);
$_SERVER += $_ENV;
