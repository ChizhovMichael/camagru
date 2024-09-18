<?php

declare(strict_types= 1);

namespace Camagru\Model;

use Camagru\Kernel\Attribute\Hidden;
use Camagru\Kernel\Attribute\LifecycleCallbacks;
use Camagru\Kernel\Attribute\Table;
use Camagru\Kernel\Model\Model;

#[Table(name: 'user', primaryKey: 'id')]
#[LifecycleCallbacks(
    createdCallbackMethod: 'createdCallbackMethod',
    updatedCallbackMethod: 'updatedCallbackMethod',
)]
class User extends Model
{
    private ?int $id = null;

    private string $username;

    private string $email;

    #[Hidden]
    private string $password;

    private bool $confirmed = false;

    #[Hidden]
    private ?string $confirmationToken = null;

    #[Hidden]
    private ?string $recoveryToken = null;

    #[Hidden]
    private bool $sendCommentNotification = true;

    private ?\DateTimeInterface $createdAt = null;

    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getRecoveryToken(): ?string
    {
        return $this->recoveryToken;
    }

    public function setRecoveryToken(?string $recoveryToken): self
    {
        $this->recoveryToken = $recoveryToken;

        return $this;
    }

    public function isSendCommentNotification(): bool
    {
        return $this->sendCommentNotification;
    }

    public function setSendCommentNotification(bool $sendCommentNotification): self
    {
        $this->sendCommentNotification = $sendCommentNotification;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Create callback.
     */
    public function createdCallbackMethod(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * Update callback.
     */
    public function updatedCallbackMethod(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
