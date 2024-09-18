<?php

declare(strict_types=1);

namespace Camagru\DTO;

class Gallery implements \JsonSerializable
{
    private int $id;

    private ?int $userId;

    private ?string $username;

    private string $file;

    private \DateTimeInterface $createdAt;

    private bool $isLiked = false;

    private array $comments = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function isLiked(): bool
    {
        return $this->isLiked;
    }

    public function getComments(): array
    {
        return $this->comments;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function setIsLiked(bool $isLiked): self
    {
        $this->isLiked = $isLiked;

        return $this;
    }

    public function setComments(array $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'user_id' => $this->getUserId(),
            'file' => $this->getFile(),
            'created_at' => $this->getCreatedAt()->format(\DateTimeInterface::RFC3339),
            'is_liked' => $this->isLiked(),
            'comments' => $this->getComments(),
            'username' => $this->getUsername(),
        ];
    }
}
