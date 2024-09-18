<?php

declare(strict_types=1);

namespace Camagru\Kernel\Exception;

class FileException extends \LogicException
{
    protected string $error = 'File exception';

    public function __construct(
        ?string $file = null,
        \Throwable $previous = null,
    ) {
        if ($file) {
            $message = sprintf("%s: %s.", $this->error, $file);
        } else {
            $message = $this->error;
        }
        
        parent::__construct($message, 500, $previous);
    }
}
