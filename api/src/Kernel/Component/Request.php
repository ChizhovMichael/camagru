<?php

declare(strict_types=1);

namespace Camagru\Kernel\Component;

class Request
{
    public Input $request;

    public Input $query;

    public Input $cookies;

    public Input $server;

    public Input $headers;

    public Input $files;

    public mixed $content;

    public string $pathInfo;

    public string $method;

    public string $basePath;

    /**
     * Slug params
     * @var array
     */
    public array $params = [];

    public function __construct(
        array $query = [], 
        array $request = [], 
        array $attributes = [], 
        array $cookies = [], 
        array $files = [], 
        array $server = [], 
        $content = null
    ) {
        $this->initialize($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    private function initialize(
        array $query = [], 
        array $request = [], 
        array $attributes = [], 
        array $cookies = [], 
        array $files = [], 
        array $server = [], 
        $content = null
    ): void {
        $this->request = new Input($request);
        $this->query = new Input($query);
        $this->cookies = new Input($cookies);
        $this->server = new Input($server);
        $this->headers = new Input($this->getHeaders());
        $this->files = new Input($files);

        $this->content = $content;
        $this->pathInfo = $this->getRequestUri();
        // $this->baseUrl = $this->getRequestUri();
        $this->basePath = $this->getBasePath();
        $this->method = $this->getMethod();
        // $this->format = null;
    }

    public static function createFromGlobals(): static
    {
        $request = self::createRequestFromFactory($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER);

        if (str_starts_with($request->getContentType(), 'application/x-www-form-urlencoded')
            && \in_array(strtoupper($request->getMethod()), ['PUT', 'DELETE', 'PATCH'], true)
        ) {
            parse_str($request->getContent(), $data);
            $request->request = new Input($data);
        }

        return $request;
    }

    private static function createRequestFromFactory(
        array $query = [], 
        array $request = [], 
        array $attributes = [], 
        array $cookies = [],
        array $files = [], 
        array $server = [], 
        $content = null
    ): self {
        return new self($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    private function getHeaders(): array
    {
        $headers = [];
        foreach ($this->server->all() as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
            } elseif (\in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'], true) && '' !== $value) {
                $headers[$key] = $value;
            }
        }

        return $headers;
    }

    /**
     * Get the value of request uri
     */ 
    private function getRequestUri(): string
    {
        return $this->server->get('REQUEST_URI', '/');
    }

    /**
     * Get the value of method
     */ 
    private function getMethod(): string
    {
        return $this->server->get('REQUEST_METHOD', 'GET');
    }

    /**
     * Get the value of content type
     */ 
    public function getContentType(): string
    {
        return $this->headers->get('CONTENT_TYPE', '');
    }

    /**
     * Get the value of base path
     */ 
    private function getBasePath(): string
    {
        return parse_url($this->getRequestUri(), PHP_URL_PATH);
    }

    public function getContent()
    {
        $currentContentIsResource = \is_resource($this->content);

        if ($currentContentIsResource) {
            rewind($this->content);

            return stream_get_contents($this->content);
        }

        if (null === $this->content || false === $this->content) {
            $this->content = file_get_contents('php://input');
        }

        return $this->content;
    }
}
