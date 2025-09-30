<?php

declare(strict_types=1);

namespace VM\Application\User\Persistence\Model;

use VM\Application\User\Persistence\Shared\Constant\ApiKeyConstant;
use VM\Infrastructure\Core\Constant\CommonConstant;
use VM\Infrastructure\Core\Constant\DbConstant;
use VM\Infrastructure\Core\Database\Model;

class ApiKey extends Model
{
    protected static string $table = DbConstant::API_KEYS_TABLE;

    protected static array $columns = [
        CommonConstant::ID,
        CommonConstant::USER_ID,
        ApiKeyConstant::API_KEY,
        ApiKeyConstant::NAME,
        ApiKeyConstant::EXPIRES_AT,
        ApiKeyConstant::IS_ACTIVE,
        ApiKeyConstant::SCOPES,
    ];
}
