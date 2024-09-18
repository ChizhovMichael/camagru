<?php

declare(strict_types=1);

namespace Camagru\DTO;

use Camagru\Model\User;

class Comment implements \JsonSerializable
{
    private int $id;

    private string $message;

    private User $user;

    private \DateTimeInterface $createdAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

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

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'message' => $this->getMessage(),
            'username' => $this->getUser()->getUsername(),
            'created_at' => $this->getCreatedAt()->format(\DateTimeInterface::RFC3339),
        ];
    }
}
