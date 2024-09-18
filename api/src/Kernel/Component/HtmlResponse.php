<?php

declare(strict_types= 1);

namespace Camagru\Kernel\Component;

class HtmlResponse extends Response
{
    public function __construct(
        mixed $data = null, 
        int $status = 200, 
        array $headers = [],
    ) {
        parent::__construct('', $status, $headers);

        if (!is_string($data)) {
            throw new \TypeError(
                sprintf(
                    '"%s": Argument $data must be a string, "%s" given.', 
                    __METHOD__, 
                    get_debug_type($data)
                )
            );
        }

        $this->setData($data);
    }

    public function setData(string $data): static
    {
        if (
            !isset($this->headers['Content-Type']) 
            || false === strpos($this->headers['Content-Type'], 'text/html')
        ) {
            $this->headers['Content-Type'] = 'text/html; charset=UTF-8';
        }

        return $this->setContent($data);
    }
}
