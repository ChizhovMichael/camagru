<?php

declare(strict_types=1);

namespace Camagru\Enum\Logger;

enum Level: string
{
    case INFO = 'info';
    case ERROR = 'error';
}