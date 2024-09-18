<?php

declare(strict_types=1);

namespace Camagru\Kernel\Component;

class JsonResponse extends Response
{
    protected mixed $data;

    public function __construct(
        mixed $data = null, 
        int $status = 200, 
        array $headers = [], 
        bool $json = false
    ) {
        parent::__construct('', $status, $headers);

        if ($json && !is_string($data) && !is_numeric($data) && !is_callable([$data, '__toString'])) {
            throw new \TypeError(
                sprintf(
                    '"%s": Argument $data must be a string or object implementing __toString(), "%s" given.', 
                    __METHOD__, 
                    get_debug_type($data)
                )
            );
        }

        $json ? $this->setJson($data) : $this->setData($data);
    }

    public function setJson(string $json): static
    {
        $this->data = $json;

        if (!isset($this->headers['Content-Type']) || 'text/javascript' === $this->headers['Content-Type']) {
            $this->headers['Content-Type'] = 'application/json';
        }

        return $this->setContent($this->data);
    }

    public function setData(mixed $data = []): static
    {
        try {
            $data = json_encode($data, 15);
        } catch (\Exception $e) {
            if ('Exception' === $e::class && str_starts_with($e->getMessage(), 'Failed calling ')) {
                throw $e->getPrevious() ?: $e;
            }
            throw $e;
        }

        if (\JSON_THROW_ON_ERROR & 15) {
            return $this->setJson($data);
        }

        if (\JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(json_last_error_msg());
        }

        return $this->setJson($data);
    }
}
