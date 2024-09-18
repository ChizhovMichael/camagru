<?php

declare(strict_types=1);

namespace Camagru\Service;

use Camagru\DTO\Security\Token;
use Camagru\DTO\Security\User as SecurityUser;
use Camagru\Model\Session;
use Camagru\Model\User;

class SecurityService
{
    public function generateToken(User $user): Token
    {
        $refreshToken = bin2hex(random_bytes(32));

        $this->generateSession($user, $refreshToken);

        $securityUser = new SecurityUser(
            id: $user->getId(),
            username: $user->getUsername(),
            roles: ['ROLE_CLIENT']
        );

        $expireAt = (new \DateTime())->modify('+24 hours');

        return new Token(
            user: $securityUser,
            token: TokenDecoder::encode([
                'user_id' => $user->getId(),
                'expire_at' => $expireAt,
            ]),
            refreshToken: $refreshToken,
            expireAt: $expireAt->format(\DateTimeInterface::RFC3339),
        );
    }

    private function generateSession(User $user, string $refreshToken): void
    {
        $session = new Session();
        $session->setUser($user);
        $session->setRefreshToken($refreshToken);
        $session->setCreatedAt(new \DateTime());
        $session->save();
    }
}
