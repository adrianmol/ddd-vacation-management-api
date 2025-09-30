<?php

declare(strict_types=1);

namespace VM\Application\User\Business;

use VM\Application\User\Persistence\Shared\Transfer\UserCriteriaTransfer;
use VM\Application\User\Persistence\Shared\Transfer\UserTransfer;
use VM\Domain\Enums\UserRoleEnum;
use VM\Infrastructure\Facade\AbstractFacade;

/**
 * @method UserBusinessFactory getFactory()
 */
class UserFacade extends AbstractFacade
{
    public function login(string $username, string $password): ?UserTransfer
    {
        return $this->getFactory()
            ->createUserReader()
            ->login($username, $password);
    }

    public function getById(int $id): ?UserTransfer
    {
        return $this->getFactory()
            ->createUserReader()
            ->getById($id);
    }

    /**
     * @return array <UserTransfer>
     */
    public function getAllByRole(?string $role = UserRoleEnum::EMPLOYEE->value): array
    {
        return $this->getFactory()
            ->createUserReader()
            ->getAllByRole($role);
    }

    public function getByCriteria(UserCriteriaTransfer $userCriteriaTransfer): ?UserTransfer
    {
        return $this->getFactory()
            ->createUserReader()
            ->getByCriteria($userCriteriaTransfer);
    }

    public function create(UserTransfer $userTransfer): UserTransfer
    {
        return $this->getFactory()
            ->createUserReader()
            ->create($userTransfer);
    }

    public function update(int $id, UserTransfer $userTransfer): UserTransfer
    {
        return $this->getFactory()
            ->createUserReader()
            ->update($id, $userTransfer);
    }

    public function deleteById(int $id): void
    {
        $this->getFactory()
            ->createUserReader()
            ->deleteById($id);
    }

    protected static function createFactory(): UserBusinessFactory
    {
        return new UserBusinessFactory();
    }
}
