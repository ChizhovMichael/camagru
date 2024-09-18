<?php

declare(strict_types=1);

namespace Camagru\Middleware;

use Camagru\Kernel\Component\Request;
use Camagru\Kernel\Component\Response;
use Camagru\Kernel\Contract\MiddlewareInterface;
use Camagru\Service\TokenDecoder;
use Closure;

class SecurityMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->cookies->get('access_token')) {
            return $next($request);
        }

        try {
            $payload = TokenDecoder::decode($request->cookies->get('access_token'));
            $request->request->set('security_user', (int)$payload['user_id'] ?? null);
        } catch (\Throwable $exception) {
            throw new \Exception('Invalid access token');
        }

        return $next($request);
    }
}
