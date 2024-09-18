<?php

declare(strict_types=1);

namespace Camagru\Kernel\Component;

class RequestStack
{
    private array $requests = [];

    public function push(Request $request): void
    {
        $this->requests[] = $request;
    }

    public function pop(): ?Request
    {
        if (!$this->requests) {
            return null;
        }

        return array_pop($this->requests);
    }
}
