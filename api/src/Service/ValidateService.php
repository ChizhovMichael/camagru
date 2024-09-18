<?php

declare(strict_types=1);

namespace Camagru\Service;

use Camagru\Kernel\Exception\ValidateException;

class ValidateService
{
    public function email(string $email): void
    {
        if (
            !filter_var($email, FILTER_VALIDATE_EMAIL)
            || !preg_match('/@.+\./', $email)
        ) {
            throw new ValidateException('Not correct email address.');
        }
    }

    public function password(string $password): void
    {
        if (strlen($password) < 8) {
            throw new ValidateException('Not correct password length.');
        }
    }
}
