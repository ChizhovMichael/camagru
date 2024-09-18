<?php

declare(strict_types=1);

namespace Camagru\Model;

use Camagru\Kernel\Attribute\Table;
use Camagru\Kernel\Model\Model;

#[Table(name: 'session', primaryKey: 'id')]
class Session extends Model
{
    private ?int $id = null;

    private ?User $user = null;

    private string $refreshToken;

    private \DateTimeInterface $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
