<?php

declare(strict_types=1);

namespace Camagru\Middleware;

use Camagru\Kernel\Component\JsonResponse;
use Camagru\Kernel\Component\Request;
use Camagru\Kernel\Component\Response;
use Camagru\Kernel\Contract\MiddlewareInterface;
use Camagru\Model\User;
use Closure;

class ConfirmMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        $fetchHeader = $request->headers->get('X_REQUESTED_WITH');
        $redirect = $fetchHeader !== 'XMLHttpRequest';

        $securityUser = $request->request->get('security_user');
        if (!$securityUser) {
            return $next($request);
        }

        $user = User::find($securityUser);
        if (!$user) {
            return $next($request);
        }
        if (!$user->isConfirmed()) {
            return $this->onConfirmationFailure($redirect);
        }

        return $next($request);
    }

    private function onConfirmationFailure(bool $redirect): JsonResponse
    {
        $data = ['message' => 'Confirmation failed'];

        if ($redirect) {
            header("Location: /confirm");
            exit;
        }

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
