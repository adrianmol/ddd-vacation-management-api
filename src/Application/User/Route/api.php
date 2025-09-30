<?php

declare(strict_types=1);

use VM\Application\User\Communication\Controller\Api\AuthApiController;
use VM\Application\User\Communication\Controller\Api\UserApiController;
use VM\Infrastructure\Core\Constant\CoreConstant;
use VM\Infrastructure\Http\Constant\HttpConstant;

return [
    [HttpConstant::METHOD_POST, CoreConstant::API_PREFIX.'/auth/login', [AuthApiController::class, 'index']],

    [HttpConstant::METHOD_GET,  CoreConstant::API_PREFIX.'/users',      [UserApiController::class, 'index']],
    [HttpConstant::METHOD_PUT,  CoreConstant::API_PREFIX.'/users',      [UserApiController::class, 'updateByEmployee']],

    [HttpConstant::METHOD_GET,  CoreConstant::API_PREFIX.'/users/{id}', [UserApiController::class, 'show']],
    [HttpConstant::METHOD_PUT,  CoreConstant::API_PREFIX.'/users/{id}', [UserApiController::class, 'updateByManager']],
    [HttpConstant::METHOD_DELETE,  CoreConstant::API_PREFIX.'/users/{id}', [UserApiController::class, 'deleteByManager']],

    [HttpConstant::METHOD_POST, CoreConstant::API_PREFIX.'/users',      [UserApiController::class, 'store']],
];
