<?php

declare(strict_types=1);

namespace Camagru\Service;

use Camagru\Kernel\Dotenv;
use Camagru\Model\User;

class FunctionLinkService
{
    public static function generateConfirmLink(User $user): string
    {
        $token = $user->getConfirmationToken();
        $baseUrl = Dotenv::get('APP_BASE_URL');

        if (!$token) {
            throw new \LogicException('No confirmation token');
        }

        return $baseUrl . '/confirm/email?token=' . $token . '&user_id=' . $user->getId();
    }

    public static function checkConfirmLink(string $token, int $userId): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        $confirmationToken = $user->getConfirmationToken();
        if (!$confirmationToken) {
            return false;
        }

        return $confirmationToken === $token;
    }

    public static function generateConfirmToken(): string
    {
        return bin2hex(random_bytes(8));
    }

    public static function generateRecoveryToken(): string
    {
        return bin2hex(random_bytes(8));
    }

    public static function generateRecoveryLink(User $user): string
    {
        $code = $user->getRecoveryToken();
        $baseUrl = Dotenv::get('APP_BASE_URL');

        if (!$code) {
            throw new \LogicException('No recovery code');
        }

        return $baseUrl . '/recovery/password?code=' . $code . '&user_id=' . $user->getId();
    }
}