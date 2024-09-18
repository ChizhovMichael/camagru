<?php

declare(strict_types=1);

namespace Camagru\DataTransform\Comment;

use Camagru\DTO\Comment as CommentDTO;
use Camagru\Kernel\Component\Request;
use Camagru\Kernel\Contract\DataTransform;
use Camagru\Model\Comment;

class ModelToDTO implements DataTransform
{
    /**
     * @param Comment $object
     * @param Request $request
     * @param array $context
     * @return CommentDTO
     */
    public function transform($object, Request $request, array $context = []): object
    {
        $output = new CommentDTO();
        $output->setUser($object->getUser());
        $output->setId($object->getId());
        $output->setMessage($object->getMessage());
        $output->setCreatedAt($object->getCreatedAt());

        return $output;
    }
}
