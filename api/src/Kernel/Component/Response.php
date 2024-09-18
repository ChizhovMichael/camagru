<?php

declare(strict_types=1);

namespace Camagru\Kernel\Component;

class Response
{

    public const HTTP_OK = 200;

    public const HTTP_CREATED = 201;

    public const HTTP_ACCEPTED = 202;

    public const HTTP_BAD_REQUEST = 400;

    public const HTTP_UNAUTHORIZED = 401;

    public const HTTP_FORBIDDEN = 403;

    public const HTTP_NOT_FOUND = 404;

    public const HTTP_INTERNAL_SERVER_ERROR = 500;

    protected int $statusCode;

    protected string $statusText;

    protected string $content;

    protected array $headers = [];

    protected array $cookies = [];

    protected string $version = '1.1';

    public static array $statusTexts = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',
        self::HTTP_OK => 'OK',
        self::HTTP_CREATED => 'Created',
        self::HTTP_ACCEPTED => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        self::HTTP_BAD_REQUEST => 'Bad Request',
        self::HTTP_UNAUTHORIZED => 'Unauthorized',
        402 => 'Payment Required',
        self::HTTP_FORBIDDEN => 'Forbidden',
        self::HTTP_NOT_FOUND => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Content Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Content',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        self::HTTP_INTERNAL_SERVER_ERROR => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    public function __construct(
        ?string $content = '', 
        int $status = 200, 
        array $headers = [], 
        array $cookies = []
    ) {
        $csrf = md5('csrf');
        $this->headers = $headers;
        $this->cookies = $cookies + ["csrf=$csrf"];
        $this->setContent($content);
        $this->setStatusCode($status ?: self::HTTP_INTERNAL_SERVER_ERROR);
    }

    
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();

        exit;
    }

    private function sendHeaders(): void
    {    
        if (headers_sent()) {
            return;
        }

        foreach ($this->headers as $name => $value) {
            header(
                $name.': '.$value, 
                0 === strcasecmp($name, 'Content-Type'), 
                $this->statusCode
            );
        }

        foreach ($this->cookies as $cookie) {
            header(
                'Set-Cookie: '.$cookie, 
                false, 
                $this->statusCode
            );
        }

        header(
            "HTTP/{$this->version} {$this->statusCode} {$this->statusText}", 
            true, 
            $this->statusCode
        );

        return;
    }

    private function sendContent(): void
    {
        echo $this->content;

        return;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content ?? '';

        return $this;
    }

    public function setStatusCode(int $code, ?string $text = null): self
    {
        $this->statusCode = $code;
        $this->statusText = $text ?? self::$statusTexts[$code] ?? 'unknown status';

        return $this;
    }
    
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function addHeader(string $key, string $value): static
    {
        $this->headers[$key] = $value;

        return $this;
    }
}
