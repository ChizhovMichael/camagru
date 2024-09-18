<?php

declare(strict_types=1);

namespace Camagru\Kernel;

use Closure;

class Pipeline
{
    protected object $passable;

    protected array $pipes = [];
    
    protected string $method = 'handle';

    public static function send(object $passable): static
    {
        $pipeline = new static;

        $pipeline->passable = $passable;

        return $pipeline;
    }

    public function through(array $pipes): self
    {
        $this->pipes = $pipes;

        return $this;
    }

    public function then(Closure $destination) 
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes),
            $this->carry(),
            function ($passable) use ($destination) {
                return $destination($passable);
            }
        );

        return $pipeline($this->passable);
    }

    public function thenReturn(): object
    {
        return $this->then(function ($passable) {
            return $passable;
        });
    }

    protected function carry(): Closure
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                if (is_callable($pipe)) {
                    return $pipe($passable, $stack);
                } elseif (is_object($pipe)) {
                    return $pipe->{$this->method}($passable, $stack);
                } elseif (is_string($pipe) && class_exists($pipe)) {
                    $pipeInstance = new $pipe;
                    return $pipeInstance->{$this->method}($passable, $stack);
                } elseif (is_array($pipe) && is_object($pipe[0]) && is_string($pipe[1])) {
                    return $pipe[0]->{$pipe[1]}($passable, $stack);
                } else {
                    throw new \InvalidArgumentException('Invalid pipe type.');
                }
            };
        };
    }
}
