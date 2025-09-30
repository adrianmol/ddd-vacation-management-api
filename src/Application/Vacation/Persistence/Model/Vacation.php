<?php

declare(strict_types=1);

namespace VM\Application\Vacation\Persistence\Model;

use VM\Application\Vacation\Persistence\Shared\Constant\VacationConstant;
use VM\Infrastructure\Core\Constant\CommonConstant;
use VM\Infrastructure\Core\Constant\DbConstant;
use VM\Infrastructure\Core\Database\Model;

class Vacation extends Model
{
    protected static string $table = DbConstant::VACATIONS_TABLE;

    protected static array $columns = [
        CommonConstant::ID,
        VacationConstant::EMPLOYEE_ID,
        VacationConstant::MANAGER_ID,
        VacationConstant::START_DATE,
        VacationConstant::END_DATE,
        VacationConstant::REASON,
        VacationConstant::STATUS,
    ];
}
