<?php

declare(strict_types=1);

namespace VM\Application\User\Persistence\Mapper;

use VM\Application\User\Persistence\Shared\Constant\UserApiConstant;
use VM\Application\User\Persistence\Shared\Transfer\UserTransfer;
use VM\Domain\Enums\UserRoleEnum;
use VM\Infrastructure\Core\Constant\CommonApiConstant;

class UserMapper
{
    public static function mapUserCollectionToUserApiCollectionResponse(array $userTransferCollection): array
    {
        $userApiCollectionResponse = [];
        foreach ($userTransferCollection as $userTransfer) {
            $userApiCollectionResponse[] = static::mapUserToEmployeeApiResponse($userTransfer);
        }

        return $userApiCollectionResponse;
    }

    public static function mapUserToEmployeeApiResponse(UserTransfer $transfer): array
    {
        return [
            CommonApiConstant::USER_ID => $transfer->getId(),
            UserApiConstant::FULL_NAME => $transfer->getFullName(),
            UserApiConstant::USERNAME => $transfer->getUsername(),
            UserApiConstant::EMAIL => $transfer->getEmail(),
            UserApiConstant::ROLE => UserRoleEnum::tryFrom($transfer->getRole())?->value ?? UserRoleEnum::EMPLOYEE->value,
        ];
    }

    public static function mapUserToUserApiResponse(UserTransfer $transfer): array
    {
        return [
            CommonApiConstant::USER_ID => $transfer->getId(),
            UserApiConstant::FULL_NAME => $transfer->getFullName(),
            UserApiConstant::USERNAME => $transfer->getUsername(),
            UserApiConstant::EMAIL => $transfer->getEmail(),
            UserApiConstant::ROLE => UserRoleEnum::tryFrom($transfer->getRole())?->value ?? UserRoleEnum::EMPLOYEE->value,
            UserApiConstant::API_KEY => $transfer->getApiKeyTransfer()?->getApiKey(),
        ];
    }
}
