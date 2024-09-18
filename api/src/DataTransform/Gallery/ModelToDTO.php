<?php

declare(strict_types=1);

namespace Camagru\DataTransform\Gallery;

use Camagru\DataTransform\Comment\ModelToDTO as CommentDataTransform;
use Camagru\DTO\Gallery as GalleryDTO;
use Camagru\Kernel\Component\Request;
use Camagru\Kernel\Contract\DataTransform;
use Camagru\Model\Comment;
use Camagru\Model\Gallery;
use Camagru\Model\Relation;

class ModelToDTO implements DataTransform
{
    public function __construct(
        private readonly CommentDataTransform $dataTransform,
    ) {
    }

    /**
     * @var Gallery $object
     * @return GalleryDTO
     */
    public function transform($object, Request $request, array $context = []): object
    {
        $currentUserLiked = false;
        $securityUser = $request->request->get('security_user');
        if ($securityUser) {
            $relation = Relation::findBy([
                'user_id:eq' => $securityUser,
                'gallery_id:eq' => $object->getId(),
            ]);
            $currentUserLiked = (bool) $relation;
        }

        $comments = [];
        if ($context['show_comments'] ?? false) {
            $comments = array_map(
                callback: function (Comment $comment) use ($request) {
                    return $this->dataTransform->transform($comment, $request);
                },
                array: Comment::findBy(
                    ['gallery_id:eq' => $object->getId()],
                    ['created_at' => 'DESC']
                ),
            );
        }

        $output = new GalleryDTO();
        $output->setId($object->getId());
        $output->setUserId($object->getUser()?->getId());
        $output->setFile($object->getFile());
        $output->setCreatedAt($object->getCreatedAt());
        $output->setIsLiked($currentUserLiked);
        $output->setComments($comments);
        $output->setUsername($object->getUser()?->getUsername());

        return $output;
    }
}
