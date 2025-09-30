<?php

declare(strict_types=1);

namespace VM\Infrastructure\Http\Response;

use VM\Infrastructure\Http\Constant\HttpConstant;

class JsonResponse extends Response
{
    public function __construct(array $data, int $status = 200)
    {
        parent::__construct(
            ['success' => true, 'data' => $data],
            $status,
            ['Content-Type' => 'application/json']
        );
    }

    public function sendUnauthorized(): self
    {
        return new self(
            [
                static::CODE => HttpConstant::STATUS_UNAUTHORIZED,
                static::MESSAGE => 'Unauthorized',
            ],
            HttpConstant::STATUS_UNAUTHORIZED,
        );
    }

    public function sendForbidden(): self
    {
        return new self(
            [
                static::CODE => HttpConstant::STATUS_FORBIDDEN,
                static::MESSAGE => 'Forbidden',
            ],
            HttpConstant::STATUS_FORBIDDEN,
        );
    }
}
