<?php

declare(strict_types=1);

namespace Camagru\Kernel\Abstract;

use Camagru\Kernel\Component\Response;
use Camagru\Kernel\Component\HtmlResponse;
use Camagru\Kernel\Component\JsonResponse;
use Camagru\Kernel\Template\TemplateBuilder;

abstract class AbstractController
{
    protected function json(
        mixed $data, 
        int $status = 200, 
        array $headers = [],
    ): JsonResponse {
        if (is_object($data) && get_class($data) !== 'stdClass') {
            return new JsonResponse(
                json_encode($data, JSON_PRETTY_PRINT), 
                $status, 
                $headers, 
                true
            );
        }

        return new JsonResponse($data, $status, $headers);
    }

    /**
     * Renders a view.
     */
    protected function render(
        string $view, 
        array $parameters = [], 
        int $status = 200, 
        array $headers = [],
    ): Response {
        $data = TemplateBuilder::build()
            ->variables($parameters)
            ->template($view)
            ->validate()
            ->parse()
        ;

        return new HtmlResponse($data, $status, $headers);
    }

    protected function redirect(string $path): void
    {
        header("Location: $path");
        exit;
    }
}
