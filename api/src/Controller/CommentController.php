<?php

declare(strict_types=1);

namespace Camagru\Controller;

use Camagru\DataTransform\Comment\ModelToDTO as DataTransform;
use Camagru\Kernel\Abstract\AbstractController;
use Camagru\Kernel\Attribute\Middleware;
use Camagru\Kernel\Component\JsonResponse;
use Camagru\Kernel\Component\Request;
use Camagru\Kernel\Component\Response;
use Camagru\Kernel\Exception\BadRequestHttpException;
use Camagru\Middleware\AuthMiddleware;
use Camagru\Middleware\CsrfMiddleware;
use Camagru\Middleware\SecurityMiddleware;
use Camagru\Model\Comment;
use Camagru\Model\Gallery;
use Camagru\Model\User;
use Camagru\Service\Logger;
use Camagru\Service\NotificationService;

class CommentController extends AbstractController
{
    public function __construct(
        private readonly DataTransform $dataTransform,
        private readonly NotificationService $notificationService,
    ) {
    }

    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: AuthMiddleware::class)]
    #[Middleware(class: CsrfMiddleware::class)]
    public function __invoke(Request $request): JsonResponse
    {
        if ($request->getContentType() !== 'application/json') {
            throw new BadRequestHttpException('Content-Type must be application/json');
        }

        try {
            [
                'gallery_id' => $galleryId,
                'comment' => $message,
            ] = json_decode($request->getContent(), true, 8, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new BadRequestHttpException('Unable to decode comment request.');
        }
        if (!$message) {
            return $this->json(['message' => 'Message is empty'], Response::HTTP_BAD_REQUEST);
        }

        $securityUser = $request->request->get('security_user');
        $user = User::find($securityUser);

        $gallery = Gallery::find((int) $galleryId);
        if (!$gallery) {
            return $this->json(['message' => 'Gallery not found.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $comment = new Comment();
            $comment->setUser($user);
            $comment->setGallery($gallery);
            $comment->setMessage($message);
            $comment->save();

            $author = $gallery->getUser();
            if ($author && $author->isSendCommentNotification()) {
                $notification = $this->notificationService->createCommentNotification($author, $comment);
                $this->notificationService->send($notification);
            }
        } catch (\Throwable $exception) {
            $message = 'Create comment handle error.';
            Logger::error(
                message: $message,
                context: [
                    'user_id' => $user->getId(),
                    'gallery_id' => $gallery->getId(),
                    'exception_message' => $exception->getMessage(),
                    'exception_place' => $exception->getFile() . ':'. $exception->getLine(),
                    'exception_trace' => $exception->getTraceAsString(),
                ],
            );

            return $this->json(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(
            data: $this->dataTransform->transform($comment, $request),
            status: Response::HTTP_OK,
        );
    }
}
