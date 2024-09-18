<?php

declare(strict_types=1);

namespace Camagru\DTO\Security;

class Token implements \JsonSerializable
{
    public function __construct(
        private readonly User $user,
        private readonly string $token,
        private readonly string $refreshToken,
        private readonly string $expireAt
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getExpireAt(): string
    {
        return $this->expireAt;
    }

    public function jsonSerialize(): array
    {
        return [
            'user' => $this->getUser(),
            'token' => $this->getToken(),
            'refresh_token' => $this->getRefreshToken(),
            'expire_at' => $this->getExpireAt(),
        ];
    }
}
