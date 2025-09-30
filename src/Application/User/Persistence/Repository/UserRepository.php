<?php

declare(strict_types=1);

namespace VM\Application\User\Persistence\Repository;

use VM\Application\User\Persistence\Model\User;
use VM\Application\User\Persistence\Shared\Constant\UserConstant;
use VM\Application\User\Persistence\Shared\Transfer\UserCriteriaTransfer;
use VM\Application\User\Persistence\Shared\Transfer\UserTransfer;
use VM\Domain\Enums\UserRoleEnum;
use VM\Domain\Utils\DateUtil;
use VM\Infrastructure\Core\Constant\CommonConstant;
use VM\Infrastructure\Core\Database\Model;

class UserRepository
{
    use DateUtil;

    public function getUserByUsername(string $username): ?UserTransfer
    {
        $userModel = self::getUserModel()->where([
            UserConstant::USERNAME => $username,
        ])[0] ?? null;

        if (null === $userModel) {
            return null;
        }

        return new UserTransfer($userModel);
    }

    public function getById(int $id): ?UserTransfer
    {
        $userModel = self::getUserModel()::find($id);

        if (null === $userModel) {
            return null;
        }

        return new UserTransfer($userModel);
    }

    public function getAllByRole(string $role = UserRoleEnum::EMPLOYEE->value): array
    {
        $userModelCollection = self::getUserModel()::where([
            UserConstant::ROLE => $role,
        ]);

        $userTransferCollection = [];
        foreach ($userModelCollection as $userModel) {
            $userTransferCollection[] = new UserTransfer($userModel);
        }

        return $userTransferCollection;
    }

    public function getByCriteria(UserCriteriaTransfer $userCriteriaTransfer): ?UserTransfer
    {
        $userModel = $this->applyFilter(self::getUserModel(), $userCriteriaTransfer);

        $firstUserModel = $userModel[0] ?? null;
        if (null === $firstUserModel) {
            return null;
        }

        return new UserTransfer($firstUserModel);
    }

    public function create(UserTransfer $userTransfer): UserTransfer
    {
        $userModel = self::getUserModel()::create($userTransfer->toArray(true, false));

        return new UserTransfer($userModel);
    }

    public function update(int $id, UserTransfer $userTransfer): UserTransfer
    {
        $userModel = self::getUserModel();
        $userModel->id = $id;

        $userModel->update($userTransfer->toArray(true, false));

        return $userTransfer->fromArray(self::getUserModel()::find($id));
    }

    public function deleteById(int $id): void
    {
        $userModel = self::getUserModel();
        $userModel->id = $id;

        $userModel->update([
            CommonConstant::DELETED_AT => $this->now(),
        ]);
    }

    protected function applyFilter(
        Model $model,
        UserCriteriaTransfer $userCriteriaTransfer,
        ?int $limit = 1,
    ): array {
        $wheres = [];
        if ($userCriteriaTransfer->getUsername()) {
            $wheres[UserConstant::USERNAME] = $userCriteriaTransfer->getUsername();
        }

        if ($userCriteriaTransfer->getEmail()) {
            $wheres[UserConstant::EMAIL] = $userCriteriaTransfer->getEmail();
        }

        return $model::orWhere($wheres, $limit);
    }

    private static function getUserModel(): User
    {
        return new User();
    }
}
