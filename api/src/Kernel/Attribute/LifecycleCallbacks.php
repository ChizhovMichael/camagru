<?php

declare(strict_types=1);

namespace Camagru\Kernel\Attribute;

#[\Attribute]
class LifecycleCallbacks
{
    public function __construct(
        private readonly ?string $createdCallbackMethod = null,
        private readonly ?string $updatedCallbackMethod = null,
        private readonly ?string $deletedCallbackMethod = null,
    ) {
    }

    public function getCreatedCallbackMethod(): ?string
    {
        return $this->createdCallbackMethod;
    }

    public function getUpdatedCallbackMethod(): ?string
    {
        return $this->updatedCallbackMethod;
    }

    public function getDeletedCallbackMethod(): ?string
    {
        return $this->deletedCallbackMethod;
    }
}
