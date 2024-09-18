<?php

declare(strict_types=1);

namespace Camagru\Kernel\Exception;

class FileExtensionNotCorrectException extends FileException
{
    protected string $error = 'File extension not correct';
}
