<?php

declare(strict_types= 1);

namespace Camagru\Kernel\Contract;

use Closure;
use Camagru\Kernel\Component\Request;
use Camagru\Kernel\Component\Response;

interface MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response;
}
