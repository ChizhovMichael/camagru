<?php

declare(strict_types=1);

namespace Camagru\Controller;

use Camagru\Kernel\Abstract\AbstractController;
use Camagru\Kernel\Attribute\Middleware;
use Camagru\Kernel\Component\JsonResponse;
use Camagru\Kernel\Component\Request;
use Camagru\Kernel\Component\Response;
use Camagru\Kernel\Exception\BadRequestHttpException;
use Camagru\Middleware\CsrfMiddleware;
use Camagru\Middleware\RedirectToMain;
use Camagru\Model\User;
use Camagru\Service\FunctionLinkService;
use Camagru\Service\Logger;
use Camagru\Service\NotificationService;
use Camagru\Service\SecurityService;
use Camagru\Service\ValidateService;

class RecoveryController extends AbstractController
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly ValidateService $validateService,
        private readonly SecurityService $securityService,
    ) {
    }

    #[Middleware(class: RedirectToMain::class)]
    public function index(): Response
    {
        return $this->render('recovery/index');
    }

    #[Middleware(class: RedirectToMain::class)]
    public function show(Request $request): Response
    {
        $token = $request->query->get('code');
        $userId = $request->query->get('user_id');
        if (!$token || !$userId) {
            $this->redirect('/recovery');
        }
        $user = User::find((int) $userId);
        if (!$user || $user->getRecoveryToken() !== $token) {
            $this->redirect('/recovery');
        }

        return $this->render('recovery/confirm');
    }

    #[Middleware(class: CsrfMiddleware::class)]
    public function recovery(Request $request): JsonResponse
    {
        if ($request->getContentType() !== 'application/json') {
            throw new BadRequestHttpException('Content-Type must be application/json');
        }

        try {
            [
                'email' => $email,
            ] = json_decode($request->getContent(), true, 8, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        try {
            $this->validateService->email($email);
        } catch (\Throwable $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $user = User::findOneBy(['email:eq' => $email]);
        if (!$user) {
            $message = 'User not found.';
            return $this->json(['message' => $message], Response::HTTP_NOT_FOUND);
        }

        $recoveryCode = FunctionLinkService::generateRecoveryToken();

        $user->setRecoveryToken($recoveryCode);
        $user->save();

        try {
            $confirmLink = FunctionLinkService::generateRecoveryLink($user);
            $notification = $this->notificationService->createRecoveryNotification($user, $confirmLink);
            $this->notificationService->send($notification);
        } catch (\Throwable $exception) {
            $message = 'Unable to send recovery email';
            Logger::error(
                message: $message,
                context: [
                    'exception_message' => $exception->getMessage(),
                    'exception_place' => $exception->getFile() . ':'. $exception->getLine(),
                    'exception_trace' => $exception->getTraceAsString(),
                ],
            );

            return $this->json(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
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
                'password' => $password,
                'confirm_password' => $confirmPassword,
                'code' => $code,
                'user_id' => $userId,
            ] = json_decode($request->getContent(), true, 8, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $user = User::find((int) $userId);
        if (!$user || $user->getRecoveryToken() !== $code) {
            $message = 'User not found.';
            return $this->json(['message' => $message], Response::HTTP_NOT_FOUND);
        }

        if ($password !== $confirmPassword) {
            $message = 'Passwords do not match.';
            return $this->json(['message' => $message], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->validateService->password($password);
        } catch (\Throwable $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            $user->setRecoveryToken(null);
            $user->save();
        } catch (\Throwable $exception) {
            $message = 'Recovery password error';
            Logger::error(
                message: $message,
                context: [
                    'exception_message' => $exception->getMessage(),
                    'exception_place' => $exception->getFile() . ':'. $exception->getLine(),
                    'exception_trace' => $exception->getTraceAsString(),
                ],
            );
        }

        $code = $this->securityService->generateToken($user);

        return $this->json($code, Response::HTTP_OK);
    }
}
