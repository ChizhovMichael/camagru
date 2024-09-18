<?php

declare(strict_types=1);

namespace Camagru\Controller;

use Camagru\Common\Constant;
use Camagru\Common\HeaderHelper;
use Camagru\DataTransform\Gallery\ModelToDTO as DataTransform;
use Camagru\DTO\CommonJsonResponse;
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
use Camagru\Model\Gallery;
use Camagru\Model\Relation;
use Camagru\Model\User;
use Camagru\Service\Logger;

class GalleryController extends AbstractController
{
    public function __construct(
        private readonly DataTransform $dataTransform,
    ) {
    }

    #[Middleware(class: SecurityMiddleware::class)]
    public function index(Request $request): JsonResponse
    {
        $securityUser = $request->request->get('security_user');

        $limit = (int)($request->query->get('items_per_page') ?? 9);
        $page = (int)($request->query->get('page') ?? 1);
        $chunk = (int)($request->query->get('chunk') ?? 0);
        $pagination = $request->query->get('pagination') !== 'false' ?? true;
        $userId = $request->query->get('user_id') ?? null;
        $author = $request->query->get('author') ?? null;
        $offset = $limit * ($page - 1);

        if ($author) {
            $gallery = Gallery::findBy(
                ['user_id:eq' => (int)$author],
                ['created_at' => 'DESC'],
            );
        } else if ($securityUser === (int)$userId) {
            $galleryIds = array_map(
                callback: static fn(Relation $relation) => $relation->getGallery()->getId(),
                array: Relation::findBy(['user_id:eq' => (int)$userId])
            );
            $gallery = count($galleryIds)
                ? Gallery::findBy(['id:in' => $galleryIds], ['created_at' => 'DESC'])
                : [];
        } else {
            $gallery = Gallery::findAll(['created_at' => 'DESC']);
        }

        $count = count($gallery);
        if ($pagination && $limit > 0) {
            $gallery = array_slice($gallery, $offset, $limit);
        }

        $gallery = array_values($gallery);

        $gallery = array_map(
            callback: function (Gallery $gallery) use ($request) {
                return $this->dataTransform->transform($gallery, $request);
            },
            array: $gallery
        );

        if ($chunk) {
            $gallery = array_chunk($gallery, 3);
        }

        return $this->json(
            data: $gallery,
            status: Response::HTTP_OK,
            headers: [
                'Content-Range' => sprintf(
                    'gallery %d-%d/%d',
                    $offset,
                    $pagination && $limit > 0 ? min($offset + $limit, $count) : $count,
                    $count
                )
            ],
        );
    }

    public function show(Request $request): JsonResponse
    {
        $galleryId = $request->params[0];
        if (!is_numeric($galleryId)) {
            $message = 'Slug must contain id.';
            return $this->json(['message' => $message], Response::HTTP_BAD_REQUEST);
        }

        $gallery = Gallery::find((int) $galleryId);
        if (!$gallery) {
            $message = 'Gallery not found.';
            return $this->json(['message' => $message], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(
            $this->dataTransform->transform($gallery, $request, [
                'show_comments' => true,
            ])
        );
    }

    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: AuthMiddleware::class)]
    #[Middleware(class: ConfirmMiddleware::class)]
    #[Middleware(class: CsrfMiddleware::class)]
    public function delete(Request $request): JsonResponse
    {
        global $config;

        if ($request->getContentType() !== 'application/json') {
            throw new BadRequestHttpException('Content-Type must be application/json');
        }

        $galleryId = $request->params[0];
        if (!is_numeric($galleryId)) {
            $message = 'Slug must contain id.';
            return $this->json(['message' => $message], Response::HTTP_BAD_REQUEST);
        }

        $securityUser = $request->request->get('security_user');
        $gallery = Gallery::find((int) $galleryId);
        if ($gallery->getUser()->getId() !== $securityUser) {
            $message = 'Access denied';
            return $this->json(['message' => $message], Response::HTTP_FORBIDDEN);
        }

        try {
            $url = parse_url($gallery->getFile());
            if (isset($url['query']) && isset($url['path'])) {
                $queryParams = [];
                parse_str($url['query'], $queryParams);
                if (isset($queryParams['direction'])) {
                    $var = $config['var_path'];
                    $filename = base64_decode(str_replace(Constant::IMAGE_PATH, '', $url['path']));
                    $direction = $queryParams['direction'];
                    if (file_exists($var.'/'.$direction.'/'.$filename)) {
                        unlink($var.'/'.$direction.'/'.$filename);
                    }
                }
            }
            $gallery->delete();
        } catch (\Throwable $exception) {
            $message = 'Delete gallery handle error.';
            Logger::error(
                message: $message,
                context: [
                    'user_id' => $securityUser,
                    'gallery_id' => $gallery->getId(),
                    'exception_message' => $exception->getMessage(),
                    'exception_place' => $exception->getFile() . ':'. $exception->getLine(),
                    'exception_trace' => $exception->getTraceAsString(),
                ],
            );

            return $this->json(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(new CommonJsonResponse(true));
    }

    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: AuthMiddleware::class)]
    #[Middleware(class: ConfirmMiddleware::class)]
    public function getAllStickers(): JsonResponse
    {
        global $config;

        return $this->json(
            array_values(
                array_map(
                    callback: static fn(string $filename) => Constant::IMAGE_PATH.base64_encode($filename),
                    array: array_diff(
                        scandir($config['var_path'].'/'.Constant::STICKERS_PATH),
                        ['.', '..'],
                    ),
                )
            )
        );
    }

    #[Middleware(class: SecurityMiddleware::class)]
    public function getImageData(Request $request): Response|JsonResponse
    {
        global $config;

        $direction = $request->query->get('direction');

        $var = $config['var_path'].'/'.$direction;
        $name = $request->params[0];

        if (!$name) {
            return $this->json(['message' => 'Sticker not found.'], Response::HTTP_BAD_REQUEST);
        }

        $name = base64_decode($name);
        $images = array_diff(scandir($var), ['.', '..'],);

        $image = array_filter(
            array: $images,
            callback: static fn (string $filename) => $name === $filename,
        );

        if (!$image || count($image) !== 1) {
            return $this->json(['message' => 'Sticker not found.'], Response::HTTP_BAD_REQUEST);
        }

        $image = array_shift($image);
        $fileContent = file_get_contents($var.'/'.$image);
        $mimeContentType = mime_content_type($var.'/'.$image);

        $response = new Response($fileContent);
        $response->addHeader('Content-Type', $mimeContentType);

        $disposition = HeaderHelper::makeDisposition(
            HeaderHelper::DISPOSITION_ATTACHMENT,
            sprintf('image-%s', $image)
        );

        $response->addHeader('Content-Disposition', $disposition);

        return $response;
    }

    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: AuthMiddleware::class)]
    #[Middleware(class: ConfirmMiddleware::class)]
    #[Middleware(class: CsrfMiddleware::class)]
    public function uploadSticker(Request $request): JsonResponse
    {
        global $config;

        $var = $config['var_path'].'/'.Constant::STICKERS_PATH;
        $file = $request->files->get('image');
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return $this->json(['message' => 'Image is not uploaded.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $imageType = mime_content_type($file['tmp_name']);
            if (!in_array($imageType, Constant::ALLOWED_TYPES, true)) {
                return $this->json(['message' => 'Unsupported image format.'], Response::HTTP_BAD_REQUEST);
            }
        } catch (\Throwable $exception) {
            $message = 'Image upload error.';
            Logger::error(
                message: $message,
                context: [
                    'exception_message' => $exception->getMessage(),
                    'exception_place' => $exception->getFile() . ':' . $exception->getLine(),
                ]
            );

            return $this->json(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $fileInfo = pathinfo($file['name']);
        $extension = $fileInfo['extension'];
        $newFileName = time() . '.' . $extension;
        $uploadFile = $var.'/'.$newFileName;

        if (!move_uploaded_file($file['tmp_name'], $uploadFile)) {
            return $this->json(['message' => 'Move file error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(
            new CommonJsonResponse(
                success: true,
                message: Constant::IMAGE_PATH.base64_encode($newFileName)
            )
        );
    }

    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: AuthMiddleware::class)]
    #[Middleware(class: ConfirmMiddleware::class)]
    #[Middleware(class: CsrfMiddleware::class)]
    public function uploadGallery(Request $request): JsonResponse
    {
        global $config;

        $var = $config['var_path'].'/'.Constant::GALLERY_PATH;
        $canvasImage = $request->files->get('canvas');
        $sourceImage = $request->files->get('video');

        if (!$canvasImage || $canvasImage['error'] !== UPLOAD_ERR_OK) {
            return $this->json(['message' => 'Image is not uploaded.'], Response::HTTP_BAD_REQUEST);
        }
        if (!$sourceImage || $sourceImage['error'] !== UPLOAD_ERR_OK) {
            return $this->json(['message' => 'Image is not uploaded.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $canvasImageType = mime_content_type($canvasImage['tmp_name']);
            if (!in_array($canvasImageType, Constant::ALLOWED_TYPES, true)) {
                return $this->json(['message' => 'Unsupported image format.'], Response::HTTP_BAD_REQUEST);
            }

            $videoImageType = mime_content_type($sourceImage['tmp_name']);
            if (!in_array($videoImageType, Constant::ALLOWED_TYPES, true)) {
                return $this->json(['message' => 'Unsupported image format.'], Response::HTTP_BAD_REQUEST);
            }
        } catch (\Throwable $exception) {
            $message = 'Image upload error.';
            Logger::error(
                message: $message,
                context: [
                    'exception_message' => $exception->getMessage(),
                    'exception_place' => $exception->getFile() . ':' . $exception->getLine(),
                ]
            );

            return $this->json(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        if (!is_dir($var)) {
            mkdir($var);
        }

        try {
            $videoTmpPath = $sourceImage['tmp_name'];
            $canvasTmpPath = $canvasImage['tmp_name'];
            $newFileName = time().'.png';
            $uploadFile = $var.'/'.$newFileName;

            $sourceImage = imagecreatefromstring(file_get_contents($videoTmpPath));
            $canvasImage = imagecreatefromstring(file_get_contents($canvasTmpPath));
            $sourceWidth = imagesx($sourceImage);
            $sourceHeight = imagesy($sourceImage);
            $canvasWidth = imagesx($canvasImage);
            $canvasHeight = imagesy($canvasImage);
            $canvasOffsetX = $canvasOffsetY = $sourceOffsetX = $sourceOffsetY = 0;

            // stickers width > image width
            if ($canvasWidth > $sourceWidth) {
                $ratio = $canvasHeight / $canvasWidth;
                $canvasImage = $this->resizeImageWithAlpha($canvasImage, $canvasWidth, $canvasHeight, $sourceWidth, $sourceWidth * $ratio);
                $canvasHeight = $sourceWidth * $ratio;
                $canvasWidth = $sourceWidth;
            }

            // stickers height > image height
            if ($canvasHeight > $sourceHeight) {
                $ratio = $canvasWidth / $canvasHeight;
                $canvasImage = $this->resizeImageWithAlpha($canvasImage, $canvasWidth, $canvasHeight, $sourceHeight * $ratio, $sourceHeight);
                $canvasWidth = $sourceHeight * $ratio;
                $canvasHeight = $sourceHeight;
            }

            // image width > stickers width
            if ($sourceWidth > $canvasWidth) {
                $ratio = $sourceHeight / $sourceWidth;
                $sourceImage = $this->resizeImageWithAlpha($sourceImage, $sourceWidth, $sourceHeight, $canvasWidth, $canvasWidth * $ratio);
                $sourceHeight = $canvasWidth * $ratio;
                $sourceWidth = $canvasWidth;
            }

            if ($sourceHeight > $canvasHeight) {
                $ratio = $sourceWidth / $sourceHeight;
                $sourceImage = $this->resizeImageWithAlpha($sourceImage, $sourceWidth, $sourceHeight, $canvasHeight * $ratio, $canvasHeight);
                $sourceWidth = $canvasHeight * $ratio;
                $sourceHeight = $canvasHeight;
            }

            // center canvas by height
            if ($canvasHeight < $sourceHeight) {
                $canvasOffsetY = ($sourceHeight - $canvasHeight) / 2;
            }

            // center canvas by width
            if ($canvasWidth < $sourceWidth) {
                $canvasOffsetX = ($sourceHeight - $canvasWidth) / 2;
            }

            // center image by height
            if ($sourceHeight < $canvasHeight) {
                $sourceOffsetY = ($canvasHeight - $sourceHeight) / 2;
            }

            // center image by width
            if ($sourceWidth < $canvasWidth) {
                $sourceOffsetX = ($canvasWidth - $sourceWidth) / 2;
            }

            $outputWidth = max($canvasWidth, $sourceWidth);
            $outputHeight = max($canvasHeight, $sourceHeight);

            $mergedImage = imagecreatetruecolor((int)$outputWidth, (int)$outputHeight);
            imagecopy($mergedImage, $sourceImage, (int)$sourceOffsetX, (int)$sourceOffsetY, 0, 0, (int)$sourceWidth, (int)$sourceHeight);
            imagecopy($mergedImage, $canvasImage, (int)$canvasOffsetX, (int)$canvasOffsetY, 0, 0, (int)$canvasWidth, (int)$canvasHeight);
            imagepng($mergedImage, $uploadFile);
            imagedestroy($sourceImage);
            imagedestroy($canvasImage);
            imagedestroy($mergedImage);
        } catch (\Throwable $exception) {
            $message = 'Image generate error.';
            Logger::error(
                message: $message,
                context: [
                    'exception_message' => $exception->getMessage(),
                    'exception_place' => $exception->getFile() . ':' . $exception->getLine(),
                ]
            );

            return $this->json(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        try {
            $securityUser = $request->request->get('security_user');
            $user = User::find($securityUser);
            $file = Constant::IMAGE_PATH.base64_encode($newFileName);

            $gallery = new Gallery();
            $gallery->setUser($user);
            $gallery->setFile($file.'?direction='.Constant::GALLERY_PATH);
            $gallery->save();
        } catch (\Throwable $exception) {
            $message = 'Image save error.';
            Logger::error(
                message: $message,
                context: [
                    'exception_message' => $exception->getMessage(),
                    'exception_place' => $exception->getFile() . ':' . $exception->getLine(),
                ]
            );

            return $this->json(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($gallery, Response::HTTP_OK);
    }

    private function resizeImageWithAlpha($srcImage, $srcWidth, $srcHeight, $newWidth, $newHeight): \GdImage|bool
    {
        $newWidth = (int)$newWidth;
        $newHeight = (int)$newHeight;
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resizedImage, false);
        $transparent = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
        imagefill($resizedImage, 0, 0, $transparent);
        imagesavealpha($resizedImage, true);
        imagesavealpha($resizedImage, true);
        imagecopyresampled($resizedImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);

        return $resizedImage;
    }
}
