<?php

declare(strict_types=1);

namespace Camagru\Middleware;

use Camagru\Kernel\Component\JsonResponse;
use Camagru\Model\Session;
use Camagru\Service\TokenDecoder;
use Closure;
use Camagru\Kernel\Component\Request;
use Camagru\Kernel\Component\Response;
use Camagru\Kernel\Contract\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): Response
    {
        // after response realizitaion
        // $response = $next($request);
        // ----
        // return $response;

        // if ($request->input('token') !== 'my-secret-token') {
        //     return redirect('home');
        // }

        $fetchHeader = $request->headers->get('X_REQUESTED_WITH');
        $redirect = $fetchHeader !== 'XMLHttpRequest';

        if (!$request->cookies->get('access_token')) {
            return $this->onSessionUndefined($redirect);
        }

        try {
            $payload = TokenDecoder::decode($request->cookies->get('access_token'));
            $securityUser = $request->request->get('security_user');
            if (!$securityUser) {
                return $this->onAuthenticationFailure();
            }
            $session = Session::findOneBy(
                ['user_id:eq' => $securityUser],
                ['id' => 'DESC']
            );
            $expireAt = new \DateTime($payload['expire_at']['date']);
            if (!$session || $expireAt < new \DateTime()) {
                return $this->onSessionUndefined($redirect);
            }
        } catch (\Throwable $exception) {
            throw new \Exception('Invalid access token');
        }

        return $next($request);
    }

    private function onAuthenticationFailure(): JsonResponse
    {
        $data = ['message' => 'Authentication failed'];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    private function onSessionUndefined(bool $redirect = false)
    {
        if ($redirect) {
            header("Location: /login");
            exit;
        }

        return $this->onAuthenticationFailure();
    }
}
