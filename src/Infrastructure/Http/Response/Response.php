<?php

declare(strict_types=1);

namespace VM\Infrastructure\Http\Response;

class Response
{
    private int $status;
    private array $headers;
    private mixed $body;

    public const string CODE = 'code';
    public const string MESSAGE = 'message';

    public function __construct(mixed $body = null, int $status = 200, array $headers = [])
    {
        $this->body = $body;
        $this->status = $status;
        $this->headers = $headers;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): mixed
    {
        return $this->body;
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        if (null !== $this->body) {
            echo is_string($this->body) ? $this->body : json_encode($this->body, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
    }
}
