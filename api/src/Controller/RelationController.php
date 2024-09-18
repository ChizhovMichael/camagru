<?php

declare(strict_types=1);

namespace Camagru\Controller;

use Camagru\DTO\CommonJsonResponse;
use Camagru\Kernel\Abstract\AbstractController;
use Camagru\Kernel\Attribute\Middleware;
use Camagru\Kernel\Component\JsonResponse;
use Camagru\Kernel\Component\Request;
use Camagru\Kernel\Component\Response;
use Camagru\Kernel\Exception\BadRequestHttpException;
use Camagru\Middleware\AuthMiddleware;
use Camagru\Middleware\CsrfMiddleware;
use Camagru\Middleware\SecurityMiddleware;
use Camagru\Model\Gallery;
use Camagru\Model\Relation;
use Camagru\Model\User;
use Camagru\Service\Logger;

class RelationController extends AbstractController
{
    private const ACTION_ADD = 'add';

    private const ACTION_REMOVE = 'remove';

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
                'action' => $action,
            ] = json_decode($request->getContent(), true, 8, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new BadRequestHttpException('Unable to decode relation request.');
        }

        $securityUser = $request->request->get('security_user');
        $user = User::find($securityUser);

        $gallery = Gallery::find((int) $galleryId);
        if (!$gallery) {
            return $this->json(['message' => 'Gallery not found.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            match (true) {
                $action === self::ACTION_ADD => $this->add($user, $gallery),
                $action === self::ACTION_REMOVE => $this->remove($user, $gallery),
            };
        } catch (\Throwable $exception) {
            $message = 'Like handle error.';
            Logger::error(
                message: $message,
                context: [
                    'action' => $action,
                    'user_id' => $user->getId(),
                    'gallery_id' => $gallery->getId(),
                    'exception_message' => $exception->getMessage(),
                    'exception_place' => $exception->getFile() . ':'. $exception->getLine(),
                    'exception_trace' => $exception->getTraceAsString(),
                ],
            );

            return $this->json(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(new CommonJsonResponse(true), Response::HTTP_OK);
    }

    private function add(User $user, Gallery $gallery): void
    {
        $like = new Relation();
        $like->setUser($user);
        $like->setGallery($gallery);
        $like->save();
    }

    private function remove(User $user, Gallery $gallery): void
    {
        $like = Relation::findOneBy([
            'user_id:eq' => $user->getId(),
            'gallery_id:eq' => $gallery->getId(),
        ]);
        $like->delete();
    }
}
