<?php

declare(strict_types=1);

namespace Camagru\DTO\Security;

class User implements \JsonSerializable
{
    public function __construct(
        private readonly int $id,
        private readonly string $username,
        private readonly array $roles,
    ){
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'username' => $this->getUsername(),
            'roles' => $this->getRoles(),
        ];
    }
}
