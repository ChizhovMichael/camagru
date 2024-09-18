<?php

declare(strict_types=1);

namespace Camagru\Middleware;

use Camagru\Kernel\Component\Request;
use Camagru\Kernel\Component\Response;
use Camagru\Kernel\Contract\MiddlewareInterface;
use Camagru\Model\User;
use Closure;
use Camagru\Service\TokenDecoder;

class RedirectToMain implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->cookies->get('access_token')) {
            return $next($request);
        }

        try {
            $payload = TokenDecoder::decode($request->cookies->get('access_token'));
            $securityUserId = $payload['user_id'] ?? null;

            if (!$securityUserId || !$this->isTokenValid($payload)) {
                return $next($request);
            }
            if (!User::find($securityUserId)) {
                return $next($request);
            }

            $this->onRedirectToMain();
        } catch (\Throwable $exception) {
            throw new \Exception('Invalid access token');
        }

        return $next($request);
    }

    private function isTokenValid(array $payload): bool
    {
        $expireAt = $payload['expire_at']['date'] ?? null;
        if (!$expireAt) {
            return false;
        }

        return new \DateTime() < new \DateTime($expireAt);
    }

    private function onRedirectToMain()
    {
        header("Location: /");
        exit;
    }
}
