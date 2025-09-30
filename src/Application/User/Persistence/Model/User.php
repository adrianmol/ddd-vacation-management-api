<?php

declare(strict_types=1);

namespace VM\Application\User\Persistence\Model;

use VM\Application\User\Persistence\Shared\Constant\UserConstant;
use VM\Infrastructure\Core\Constant\CommonConstant;
use VM\Infrastructure\Core\Constant\DbConstant;
use VM\Infrastructure\Core\Database\Model;

class User extends Model
{
    protected static string $table = DbConstant::USER_TABLE;

    protected static array $columns = [
        CommonConstant::ID,
        UserConstant::FULL_NAME,
        UserConstant::USERNAME,
        UserConstant::EMAIL,
        UserConstant::PASSWORD,
        UserConstant::ROLE,
        CommonConstant::CREATED_AT,
        CommonConstant::UPDATED_AT,
    ];
}
