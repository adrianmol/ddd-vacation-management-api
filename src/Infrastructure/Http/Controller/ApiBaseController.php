<?php

declare(strict_types=1);

namespace VM\Infrastructure\Http\Controller;

use VM\Domain\Enums\UserRoleEnum;
use VM\Infrastructure\Http\Response\JsonResponse;

class ApiBaseController
{
    protected function json(array $data = [], int $status = 200): JsonResponse
    {
        return new JsonResponse($data, $status);
    }

    protected function isRoleManager(?string $role): bool
    {
        return UserRoleEnum::MANAGER->value === $role;
    }
}
