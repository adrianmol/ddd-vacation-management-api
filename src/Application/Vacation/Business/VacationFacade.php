<?php

declare(strict_types=1);

namespace VM\Application\Vacation\Business;

use VM\Application\Vacation\Persistence\Shared\Transfer\VacationCriteriaTransfer;
use VM\Application\Vacation\Persistence\Shared\Transfer\VacationTransfer;
use VM\Domain\Enums\VacationStatusEnum;
use VM\Infrastructure\Facade\AbstractFacade;

/**
 * @method VacationBusinessFactory getFactory()
 */
class VacationFacade extends AbstractFacade
{
    public function getByStatus(?string $status): array
    {
        return $this->getFactory()
            ->createVacationReader()
            ->getByStatus($status);
    }

    public function getAllByCriteria(VacationCriteriaTransfer $vacationCriteriaTransfer): array
    {
        return $this->getFactory()
            ->createVacationReader()
            ->getAllByCriteria($vacationCriteriaTransfer);
    }

    public function getByEmployeeIdByStatus(int $employeeId, ?string $status = VacationStatusEnum::PENDING->value): array
    {
        return $this->getFactory()
            ->createVacationReader()
            ->getByEmployeeIdByStatus($employeeId, $status);
    }

    public function create(VacationTransfer $vacationTransfer): VacationTransfer
    {
        return $this->getFactory()
            ->createVacationReader()
            ->create($vacationTransfer);
    }

    public function updateStatusById(int $vacationId, string $status): bool
    {
        return $this->getFactory()
            ->createVacationReader()
            ->updateStatusById($vacationId, $status);
    }

    public function deleteByCriteria(VacationCriteriaTransfer $vacationCriteriaTransfer): void
    {
        $this->getFactory()
            ->createVacationReader()
            ->deleteByCriteria($vacationCriteriaTransfer);
    }

    protected static function createFactory(): VacationBusinessFactory
    {
        return new VacationBusinessFactory();
    }
}
