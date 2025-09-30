<?php

declare(strict_types=1);

namespace VM\Domain\Enums;

enum UserRoleEnum: string
{
    case ADMIN = 'admin';

    case MANAGER = 'manager';

    case EMPLOYEE = 'employee';
}
