<?php

declare(strict_types=1);

namespace Camagru\Kernel\Exception;

class FileNotFoundException extends FileException
{
    protected string $error = 'File not found';
}
