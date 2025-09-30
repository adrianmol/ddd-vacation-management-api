<?php

declare(strict_types=1);

use VM\Application\Vacation\Communication\Controller\Api\VacationApiController;
use VM\Infrastructure\Core\Constant\CoreConstant;
use VM\Infrastructure\Http\Constant\HttpConstant;

return [
    [HttpConstant::METHOD_GET,  CoreConstant::API_PREFIX.'/vacations',                               [VacationApiController::class, 'index']],
    [HttpConstant::METHOD_POST,  CoreConstant::API_PREFIX.'/vacations',                              [VacationApiController::class, 'store']],
    [HttpConstant::METHOD_POST,  CoreConstant::API_PREFIX.'/vacations/{vacationId}/action/{action}', [VacationApiController::class, 'action']],
];
