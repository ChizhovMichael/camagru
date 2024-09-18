<?php

namespace Camagru\Model;

use Camagru\Enum\Logger\Level;
use Camagru\Kernel\Attribute\Table;
use Camagru\Kernel\Model\Model;

#[Table(name: 'log')]
class Log extends Model
{
    private Level $level;

    private string $message;

    private array $context = [];

    public function getLevel(): Level
    {
        return $this->level;
    }

    public function setLevel(Level $level): self
    {
        $this->level = $level;

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

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): self
    {
        $this->context = $context;

        return $this;
    }
}