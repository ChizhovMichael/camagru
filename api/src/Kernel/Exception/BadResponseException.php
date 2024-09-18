<?php

declare(strict_types= 1);

namespace Camagru\Kernel\Exception;

class BadResponseException extends \LogicException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 500);
    }
}
