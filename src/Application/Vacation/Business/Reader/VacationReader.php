<?php

declare(strict_types=1);

namespace VM\Application\Vacation\Business\Reader;

use VM\Application\Vacation\Persistence\Repository\VacationRepository;
use VM\Application\Vacation\Persistence\Shared\Transfer\VacationCriteriaTransfer;
use VM\Application\Vacation\Persistence\Shared\Transfer\VacationTransfer;
use VM\Domain\Enums\VacationStatusEnum;

class VacationReader
{
    public function __construct(
        protected VacationRepository $vacationRepository,
    ) {
    }

    public function create(VacationTransfer $vacationTransfer): VacationTransfer
    {
        return $this->vacationRepository->create($vacationTransfer);
    }

    public function getByStatus(?string $status): array
    {
        return $this->vacationRepository->getByStatus($status);
    }

    public function getAllByCriteria(VacationCriteriaTransfer $vacationCriteriaTransfer): array
    {
        return $this->vacationRepository->getAllByCriteria($vacationCriteriaTransfer);
    }

    public function getById(int $id): VacationTransfer
    {
        return $this->vacationRepository->getById($id);
    }

    public function updateStatusById(int $vacationId, string $status): bool
    {
        $newVacationStatus = VacationStatusEnum::tryFrom($status);
        if (null === $newVacationStatus) {
            return false;
        }

        $vacationTransfer = $this->vacationRepository->getById($vacationId);
        if (null === $vacationTransfer) {
            return false;
        }

        $previousVacationStatus = VacationStatusEnum::tryFrom($vacationTransfer->getStatus());
        if (
            null === $previousVacationStatus
            || (!$previousVacationStatus->canApprove() && !$previousVacationStatus->canReject())
        ) {
            return false;
        }

        $this->vacationRepository->updateStatusById($vacationId, $status);

        return true;
    }

    public function deleteByCriteria(VacationCriteriaTransfer $vacationCriteriaTransfer): void
    {
        $this->vacationRepository->deleteByCriteria($vacationCriteriaTransfer);
    }

    public function getByEmployeeIdByStatus(int $employeeId, ?string $status = VacationStatusEnum::PENDING->value): array
    {
        return $this->vacationRepository->getByEmployeeIdByStatus($employeeId, $status);
    }
}
