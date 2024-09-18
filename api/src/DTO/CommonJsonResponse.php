<?php

declare(strict_types=1);

namespace Camagru\DTO;

class CommonJsonResponse implements \JsonSerializable
{
    public function __construct(
        private readonly bool $success,
        private readonly ?string $message = null,
    ) {
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function jsonSerialize(): array
    {
        $array = [
            'success' => $this->isSuccess(),
        ];

        if ($this->getMessage()) {
            $array += [
                'message' => $this->getMessage(),
            ];
        }

        return $array;
    }
}
