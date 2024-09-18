<?php

declare(strict_types=1);

namespace Camagru\Model;

use Camagru\Kernel\Attribute\LifecycleCallbacks;
use Camagru\Kernel\Attribute\Table;
use Camagru\Kernel\Model\Model;

#[Table(name: 'notification', primaryKey: 'id')]
#[LifecycleCallbacks(
    createdCallbackMethod: 'createdCallbackMethod',
)]
class Notification extends Model
{
    private ?int $id = null;

    private ?User $user = null;

    private string $destination;

    private string $subject;

    private string $message;

    private array $headers = [];

    private ?\DateTimeInterface $createdAt = null;

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

    public function getDestination(): string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): self
    {
        $this->destination = $destination;

        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

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

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

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

    /**
     * Create callback.
     */
    public function createdCallbackMethod(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
