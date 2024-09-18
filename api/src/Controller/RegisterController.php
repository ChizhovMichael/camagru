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
use JsonException;

class RegisterController extends AbstractController
{
    public function __construct(
        private readonly SecurityService $securityService,
        private readonly ValidateService $validateService,
        private readonly NotificationService $notificationService,
    ) {
    }

    #[Middleware(class: RedirectToMain::class)]
    public function index(): Response
    {
        return $this->render('register');
    }

    #[Middleware(class: CsrfMiddleware::class)]
    public function register(Request $request): JsonResponse
    {
        if ($request->getContentType() !== 'application/json') {
            throw new BadRequestHttpException('Content-Type must be application/json');
        }

        try {
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => $password,
            ] = json_decode($request->getContent(), true, 8, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        try {
            $this->validateService->email($email);
            $this->validateService->password($password);
        } catch (\Throwable $exception) {
            return $this->json(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $firstName = trim($firstName);
        $lastName = trim($lastName);
        $email = trim($email);
        $password = trim($password);

        if (!$firstName || !$lastName || !$email || !$password) {
            return $this->json(['message' => 'Not correct user data.'], Response::HTTP_BAD_REQUEST);
        }

        $username = $firstName.' '.$lastName;
        $user = User::findOneBy(['email:eq' => $email]);
        if ($user) {
            $message = 'User already registered.';
            return $this->json(['message' => $message], Response::HTTP_FORBIDDEN);
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $confirmationToken = FunctionLinkService::generateConfirmToken();

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($passwordHash);
        $user->setConfirmationToken($confirmationToken);
        $user->save();

        $token = $this->securityService->generateToken($user);

        try {
            $confirmLink = FunctionLinkService::generateConfirmLink($user);
            $notification = $this->notificationService->createConfirmNotification($user, $confirmLink);
            $this->notificationService->send($notification);
        } catch (\Throwable $exception) {
            Logger::error(
                message: 'Unable to send confirmation email',
                context: [
                    'exception_message' => $exception->getMessage(),
                    'exception_place' => $exception->getFile() . ':'. $exception->getLine(),
                    'exception_trace' => $exception->getTraceAsString(),
                ],
            );
        }

        return $this->json($token, Response::HTTP_OK);
    }
}