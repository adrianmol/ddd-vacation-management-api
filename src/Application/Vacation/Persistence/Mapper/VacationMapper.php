<?php

declare(strict_types=1);

namespace VM\Application\Vacation\Persistence\Mapper;

use VM\Application\Vacation\Persistence\Shared\Constant\VacationApiConstant;
use VM\Application\Vacation\Persistence\Shared\Transfer\VacationTransfer;
use VM\Infrastructure\Core\Constant\CommonConstant;

class VacationMapper
{
    public static function mapVacationCollectionToVacationApiResponseCollection(array $vacationTransferCollection): array
    {
        $vacationResponseCollection = [];
        foreach ($vacationTransferCollection as $vacationTransfer) {
            $vacationResponseCollection[] = static::mapEmployeeVacationToVacationApiResponse($vacationTransfer);
        }

        return $vacationResponseCollection;
    }

    public static function mapEmployeeVacationToVacationApiResponse(VacationTransfer $vacationTransfer): array
    {
        return [
            CommonConstant::ID => $vacationTransfer->getId(),
            VacationApiConstant::EMPLOYEE_ID => $vacationTransfer->getEmployeeId(),
            VacationApiConstant::MANAGER_ID => $vacationTransfer->getManagerId(),
            VacationApiConstant::START_DATE => $vacationTransfer->getStartDate(),
            VacationApiConstant::END_DATE => $vacationTransfer->getEndDate(),
            VacationApiConstant::REASON => $vacationTransfer->getReason(),
            VacationApiConstant::STATUS => $vacationTransfer->getStatus(),
        ];
    }

    public static function mapVacationToVacationApiResponse(VacationTransfer $vacationTransfer): array
    {
        return [
            CommonConstant::ID => $vacationTransfer->getId(),
            VacationApiConstant::STATUS_CHANGED_BY_MANAGER_ID => $vacationTransfer->getManagerId(),
            VacationApiConstant::START_DATE => $vacationTransfer->getStartDate(),
            VacationApiConstant::END_DATE => $vacationTransfer->getEndDate(),
            VacationApiConstant::REASON => $vacationTransfer->getReason(),
            VacationApiConstant::STATUS => $vacationTransfer->getStatus(),
        ];
    }
}
