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
use Camagru\Middleware\ConfirmMiddleware;
use Camagru\Middleware\CsrfMiddleware;
use Camagru\Middleware\SecurityMiddleware;
use Camagru\Model\User;
use Camagru\Service\FunctionLinkService;
use Camagru\Service\Logger;
use Camagru\Service\NotificationService;
use Camagru\Service\ValidateService;

class SettingController extends AbstractController
{
    public function __construct(
        private readonly ValidateService $validateService,
        private readonly NotificationService $notificationService,
    ) {
    }

    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: AuthMiddleware::class)]
    #[Middleware(class: ConfirmMiddleware::class)]
    public function index()
    {
        $this->redirect('/setting/profile');
    }

    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: AuthMiddleware::class)]
    #[Middleware(class: ConfirmMiddleware::class)]
    public function profile(Request $request): Response
    {
        $securityUser = $request->request->get('security_user');
        $user = User::find($securityUser);

        return $this->render('setting/profile', [
            'header' => Constant::HEADER,
            'footer' => Constant::FOOTER,
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'send_comment' => $user->isSendCommentNotification(),
        ]);
    }

    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: AuthMiddleware::class)]
    #[Middleware(class: ConfirmMiddleware::class)]
    public function activity(Request $request): Response
    {
        $securityUser = $request->request->get('security_user');
        $user = User::find($securityUser);

        return $this->render('setting/activity', [
            'header' => Constant::HEADER,
            'footer' => Constant::FOOTER,
            'username' => $user->getUsername(),
        ]);
    }

    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: AuthMiddleware::class)]
    #[Middleware(class: ConfirmMiddleware::class)]
    public function password(Request $request): Response
    {
        $securityUser = $request->request->get('security_user');
        $user = User::find($securityUser);

        return $this->render('setting/password', [
            'header' => Constant::HEADER,
            'footer' => Constant::FOOTER,
            'username' => $user->getUsername(),
        ]);
    }

    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: AuthMiddleware::class)]
    #[Middleware(class: CsrfMiddleware::class)]
    public function changeProfile(Request $request): JsonResponse
    {
        if ($request->getContentType() !== 'application/json') {
            throw new BadRequestHttpException('Content-Type must be application/json');
        }

        try {
            [
                'username' => $username,
                'email' => $email,
                'send_comment' => $sendComment,
            ] = json_decode($request->getContent(), true, 8, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $username = trim($username);
        $email = trim($email);

        if (!$username || !$email) {
            return $this->json(['message' => 'Not correct user data.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->validateService->email($email);
        } catch (\Throwable $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $securityUser = $request->request->get('security_user');
        $user = User::find($securityUser);
        if (!$user) {
            throw new BadRequestHttpException('Authentication failed.');
        }

        $confirmationToken = FunctionLinkService::generateConfirmToken();

        try {
            $user->setUsername($username);
            if ($user->getEmail() !== $email) {
                $user->setEmail($email);
                $user->setConfirmed(false);
                $user->setConfirmationToken($confirmationToken);
            }
            $user->setSendCommentNotification($sendComment);
            $user->save();
        } catch (\Throwable $exception) {
            $message = 'Change profile error.';
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

        try {
            if ($user->getEmail() !== $email) {
                $confirmLink = FunctionLinkService::generateConfirmLink($user);
                $notification = $this->notificationService->createConfirmNotification($user, $confirmLink);
                $this->notificationService->send($notification);
            }
        } catch (\Throwable $exception) {
            $message = 'Unable to send confirmation email';
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

        return $this->json($user);
    }

    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: AuthMiddleware::class)]
    #[Middleware(class: CsrfMiddleware::class)]
    public function changePassword(Request $request): JsonResponse
    {
        if ($request->getContentType() !== 'application/json') {
            throw new BadRequestHttpException('Content-Type must be application/json');
        }

        try {
            [
                'password' => $password,
                'confirm_password' => $confirmPassword,
            ] = json_decode($request->getContent(), true, 8, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new BadRequestHttpException($e->getMessage());
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

        $securityUser = $request->request->get('security_user');
        $user = User::find($securityUser);
        if (!$user) {
            throw new BadRequestHttpException('Authentication failed.');
        }

        if (password_verify($password, $user->getPassword())) {
            $message = 'Password not changed.';
            return $this->json(['message' => $message], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            $user->save();
        } catch (\Throwable $exception) {
            $message = 'Change password error.';
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

        return $this->json($user);
    }
}
