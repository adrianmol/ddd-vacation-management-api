<?php

declare(strict_types=1);

namespace VM\Application\User\Communication\Controller\Api;

use VM\Application\User\Business\UserFacade;
use VM\Application\User\Communication\Request\GetUserRequest;
use VM\Application\User\Communication\Request\StoreUserRequest;
use VM\Application\User\Communication\Request\UpdateUserRequest;
use VM\Application\User\Persistence\Mapper\UserMapper;
use VM\Application\User\Persistence\Shared\Transfer\UserCriteriaTransfer;
use VM\Application\User\Persistence\Shared\Transfer\UserTransfer;
use VM\Infrastructure\Http\BaseRequest;
use VM\Infrastructure\Http\Constant\HttpConstant;
use VM\Infrastructure\Http\Controller\ApiBaseController;
use VM\Infrastructure\Http\Response\JsonResponse;
use VM\Infrastructure\Http\Response\Response;

class UserApiController extends ApiBaseController
{
    public function __construct(
        protected UserFacade $userFacade,
    ) {
    }

    public function index(GetUserRequest $request): JsonResponse
    {
        $userTransfer = $this->userFacade->getById($request->userId());
        if (!$this->isRoleManager($userTransfer?->getRole())) {
            return $this->json()->sendForbidden();
        }

        $userTransferCollection = $this->userFacade->getAllByRole();

        return $this->json(
            UserMapper::mapUserCollectionToUserApiCollectionResponse($userTransferCollection)
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $request->validation();
        $userTransfer = new UserTransfer($request->body);
        $existUserResponse = $this->checkIfUserExists($userTransfer);

        if ($existUserResponse) {
            return $existUserResponse;
        }

        $userTransfer = $this->userFacade->create($userTransfer);

        return $this->json(
            UserMapper::mapUserToUserApiResponse($userTransfer),
            HttpConstant::STATUS_CREATED
        );
    }

    public function updateByEmployee(UpdateUserRequest $request): JsonResponse
    {
        $request->validation();

        $userTransfer = new UserTransfer($request->body);
        $userTransfer = $this->userFacade->update($request->userId(), $userTransfer);

        return $this->json(
            UserMapper::mapUserToUserApiResponse($userTransfer),
            HttpConstant::STATUS_OK
        );
    }

    public function updateByManager(int $id, UpdateUserRequest $request): JsonResponse
    {
        $userTransfer = $this->userFacade->getById($request->userId());
        if (!$this->isRoleManager($userTransfer?->getRole())) {
            return $this->json()->sendForbidden();
        }

        $request->validation();

        $userTransfer = new UserTransfer($request->body);
        $userTransfer = $this->userFacade->update($id, $userTransfer);

        return $this->json(
            UserMapper::mapUserToUserApiResponse($userTransfer),
            HttpConstant::STATUS_OK
        );
    }

    public function deleteByManager(int $id, BaseRequest $request): JsonResponse
    {
        $userTransfer = $this->userFacade->getById($request->userId());
        if (!$this->isRoleManager($userTransfer?->getRole())) {
            return $this->json()->sendForbidden();
        }

        $this->userFacade->deleteById($id);

        return $this->json(
            status: HttpConstant::STATUS_NO_CONTENT
        );
    }

    protected function checkIfUserExists(UserTransfer $userTransfer): bool|JsonResponse
    {
        $existUserTransfer = $this->userFacade->getByCriteria(
            (new UserCriteriaTransfer())
                ->setEmail($userTransfer->getEmail())
                ->setUsername($userTransfer->getUsername())
        );

        if ($existUserTransfer) {
            return $this->json(
                [
                    Response::CODE => HttpConstant::STATUS_BAD_REQUEST,
                    Response::MESSAGE => 'This email address or username is already registered.',
                ],
                HttpConstant::STATUS_BAD_REQUEST
            );
        }

        return false;
    }
}
