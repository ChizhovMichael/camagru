<?php

declare(strict_types=1);

namespace Camagru\Service;

use Camagru\Enum\Logger\Level;
use Camagru\Model\Log;

class Logger
{
    public static function error(string $message, array $context = []): Log
    {
        $log = new Log();
        $log->setLevel(Level::ERROR);
        $log->setMessage($message);
        $log->setContext($context);
        $log->save();

        return $log;
    }
}