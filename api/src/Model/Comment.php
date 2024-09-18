<?php

declare(strict_types=1);

namespace Camagru\Model;

use Camagru\Kernel\Attribute\LifecycleCallbacks;
use Camagru\Kernel\Attribute\Table;
use Camagru\Kernel\Model\Model;

#[Table(name: 'comment', primaryKey: 'id')]
#[LifecycleCallbacks(createdCallbackMethod: 'createdCallbackMethod')]
class Comment extends Model
{
    private ?int $id = null;

    private User $user;

    private Gallery $gallery;

    private string $message;

    private ?\DateTimeInterface $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getGallery(): Gallery
    {
        return $this->gallery;
    }

    public function setGallery(Gallery $gallery): self
    {
        $this->gallery = $gallery;

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
