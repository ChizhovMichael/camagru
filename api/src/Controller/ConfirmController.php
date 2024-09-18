<?php

declare(strict_types=1);

namespace Camagru\Controller;

use Camagru\Common\Constant;
use Camagru\Kernel\Abstract\AbstractController;
use Camagru\Kernel\Attribute\Middleware;
use Camagru\Kernel\Component\JsonResponse;
use Camagru\Kernel\Component\Request;
use Camagru\Kernel\Component\Response;
use Camagru\Kernel\Exception\BadRequestHttpException;
use Camagru\Middleware\AuthMiddleware;
use Camagru\Middleware\CsrfMiddleware;
use Camagru\Middleware\SecurityMiddleware;
use Camagru\Model\User;
use Camagru\Service\FunctionLinkService;
use Camagru\Service\Logger;
use Camagru\Service\NotificationService;
use Camagru\Service\SecurityService;
use JsonException;

class ConfirmController extends AbstractController
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly SecurityService $securityService,
    ) {
    }

    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: AuthMiddleware::class)]
    public function index(Request $request): Response
    {
        $securityUser = $request->request->get('security_user');
        $user = User::find($securityUser);

        if ($user->isConfirmed()) {
            $this->redirect('/');
        }

        return $this->render('confirm/index', [
            'header' => Constant::HEADER,
            'footer' => Constant::FOOTER,
            'username' => $user->getUsername(),
        ]);
    }

    #[Middleware(class: SecurityMiddleware::class)]
    public function show(Request $request): Response
    {
        $securityUser = $request->request->get('security_user');
        $token = $request->query->get('token');
        $userId = $request->query->get('user_id');
        if (!$token || !$userId) {
            $this->redirect('/confirm');
        }
        if ($securityUser && $securityUser !== (int)$userId) {
            $this->redirect('/confirm');
        }
        $user = User::find((int) $userId);
        if ($user?->isConfirmed()) {
            $this->redirect('/');
        }

        return $this->render('confirm/confirm');
    }

    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: AuthMiddleware::class)]
    #[Middleware(class: CsrfMiddleware::class)]
    public function resend(Request $request): JsonResponse
    {
        $securityUser = $request->request->get('security_user');
        $user = User::find($securityUser);
        if ($user->isConfirmed()) {
            $message = 'User already confirmed.';
            return $this->json(['message' => $message], Response::HTTP_BAD_REQUEST);
        }

        if (!$user->getConfirmationToken()) {
            $message = 'User does not have confirmation token.';
            return $this->json(['message' => $message], Response::HTTP_BAD_REQUEST);
        }

        try {
            $confirmLink = FunctionLinkService::generateConfirmLink($user);
            $notification = $this->notificationService->createConfirmNotification($user, $confirmLink);
            $this->notificationService->send($notification);
        } catch (\Throwable $exception) {
            $message = 'Unable to resend confirmation email';
            Logger::error(
                message: $message,
                context: [
                    'exception_message' => $exception->getMessage(),
                    'exception_place' => $exception->getFile() . ':'. $exception->getLine(),
                    'exception_trace' => $exception->getTraceAsString(),
                ],
            );

            return $this->json(['message' => $message], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(null, Response::HTTP_OK);
    }

    #[Middleware(class: CsrfMiddleware::class)]
    public function confirm(Request $request): JsonResponse
    {
        if ($request->getContentType() !== 'application/json') {
            throw new BadRequestHttpException('Content-Type must be application/json');
        }

        try {
            [
                'user_id' => $userId,
                'token' => $confirmationToken,
            ] = json_decode($request->getContent(), true, 8, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new BadRequestHttpException('Unable to decode refresh token request.');
        }

        if (!$confirmationToken || !$userId) {
            $message = 'Token or user_id not found.';
            return $this->json(['message' => $message], Response::HTTP_BAD_REQUEST);
        }

        $user = User::find((int)$userId);
        if (!$user) {
            $message = 'User not found.';
            return $this->json(['message' => $message], Response::HTTP_BAD_REQUEST);
        }

        if (!FunctionLinkService::checkConfirmLink($confirmationToken, $user->getId())) {
            $message = 'User verification data is incorrect.';
            return $this->json(['message' => $message], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user->setConfirmed(true);
            $user->setConfirmationToken(null);
            $user->save();
        } catch (\Throwable $exception) {
            $message = 'Confirm user error';
            Logger::error(
                message: $message,
                context: [
                    'exception_message' => $exception->getMessage(),
                    'exception_place' => $exception->getFile() . ':'. $exception->getLine(),
                    'exception_trace' => $exception->getTraceAsString(),
                ],
            );
        }

        $token = $this->securityService->generateToken($user);

        return $this->json($token, Response::HTTP_OK);
    }
}