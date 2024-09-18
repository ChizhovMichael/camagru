<?php

declare(strict_types=1);

namespace Camagru\Kernel\Exception;

class BadRequestHttpException extends HttpException
{
    public function __construct(
        string $message = "",
        \Throwable $previous = null,
    ) {
        parent::__construct($message, 400, $previous);
    }
}
