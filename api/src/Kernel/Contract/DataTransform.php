<?php

declare(strict_types=1);

namespace Camagru\Kernel\Contract;

use Camagru\Kernel\Component\Request;

interface DataTransform
{
    public function transform($object, Request $request, array $context = []): object;
}
