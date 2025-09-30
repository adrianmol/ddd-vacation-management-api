<?php

declare(strict_types=1);

namespace VM\Application\User\Business\Reader;

use VM\Application\User\Business\ApiKeyFacade;
use VM\Application\User\Persistence\Repository\UserRepository;
use VM\Application\User\Persistence\Shared\Transfer\UserCriteriaTransfer;
use VM\Application\User\Persistence\Shared\Transfer\UserTransfer;
use VM\Application\Vacation\Business\VacationFacade;
use VM\Application\Vacation\Persistence\Shared\Transfer\VacationCriteriaTransfer;
use VM\Domain\Enums\UserRoleEnum;

class UserReader
{
    public function __construct(
        protected UserRepository $userRepository,
        protected ApiKeyFacade $apiKeyFacade,
        protected VacationFacade $vacationFacade,
    ) {
    }

    public function login(string $username, string $password): ?UserTransfer
    {
        $userTransfer = $this->userRepository->getUserByUsername($username);
        if (null === $userTransfer) {
            return null;
        }

        if (!password_verify($password, $userTransfer->getPassword())) {
            return null;
        }

        $apiKeyTransfer = $this->apiKeyFacade->createApiKey($userTransfer->getId());

        return $userTransfer
            ->setPassword(null)
            ->setApiKeyTransfer($apiKeyTransfer);
    }

    public function getById(int $id): ?UserTransfer
    {
        return $this->userRepository->getById($id);
    }

    /**
     * @return array <UserTransfer>
     */
    public function getAllByRole(string $role = UserRoleEnum::EMPLOYEE->value): array
    {
        return $this->userRepository->getAllByRole($role);
    }

    public function getByCriteria(UserCriteriaTransfer $userCriteriaTransfer): ?UserTransfer
    {
        return $this->userRepository->getByCriteria($userCriteriaTransfer);
    }

    public function create(UserTransfer $userTransfer): UserTransfer
    {
        if (null === $userTransfer->getRole()) {
            $userTransfer->setRole(UserRoleEnum::EMPLOYEE->value);
        }

        $userTransfer->setPassword(password_hash($userTransfer->getPassword(), PASSWORD_DEFAULT));

        $userTransfer = $this->userRepository->create($userTransfer);
        $apiKeyTransfer = $this->apiKeyFacade->createApiKey($userTransfer->getId());

        return $userTransfer
            ->setPassword(null)
            ->setApiKeyTransfer($apiKeyTransfer);
    }

    public function update(int $id, UserTransfer $userTransfer): UserTransfer
    {
        return $this->userRepository->update($id, $userTransfer);
    }

    public function deleteById(int $id): void
    {
        $this->userRepository->deleteById($id);
        $this->vacationFacade->deleteByCriteria(
            (new VacationCriteriaTransfer())->setEmployeeId($id)
        );
    }
}
