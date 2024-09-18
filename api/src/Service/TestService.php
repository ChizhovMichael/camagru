<?php



namespace Camagru\Service;

class TestService
{
    public function __construct(
        private readonly string $var = "Hello readonly"
    ) {
    }

    public function sayHello()
    {
        echo $this->var;
    }
}
