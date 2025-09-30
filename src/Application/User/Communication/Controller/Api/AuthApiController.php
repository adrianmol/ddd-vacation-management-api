<?php

declare(strict_types=1);

namespace VM\Application\User\Communication\Controller\Api;

use VM\Application\User\Business\UserFacade;
use VM\Application\User\Communication\Request\LoginRequest;
use VM\Application\User\Persistence\Mapper\UserMapper;
use VM\Infrastructure\Http\Controller\ApiBaseController;
use VM\Infrastructure\Http\Response\JsonResponse;

class AuthApiController extends ApiBaseController
{
    public function index(LoginRequest $request): JsonResponse
    {
        $request->validation();
        $userTransfer = (new UserFacade())
            ->login($request->getUsername(), $request->getPassword());

        if (null === $userTransfer || !$userTransfer->getApiKeyTransfer()?->getApiKey()) {
            return $this->json()->sendUnauthorized();
        }

        return $this->json(
            UserMapper::mapUserToUserApiResponse($userTransfer)
        );
    }
}
