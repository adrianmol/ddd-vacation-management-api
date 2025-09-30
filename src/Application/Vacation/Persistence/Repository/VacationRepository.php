<?php

declare(strict_types=1);

namespace VM\Application\Vacation\Persistence\Repository;

use VM\Application\Vacation\Persistence\Model\Vacation;
use VM\Application\Vacation\Persistence\Shared\Constant\VacationConstant;
use VM\Application\Vacation\Persistence\Shared\Transfer\VacationCriteriaTransfer;
use VM\Application\Vacation\Persistence\Shared\Transfer\VacationTransfer;
use VM\Infrastructure\Core\Constant\CommonConstant;
use VM\Infrastructure\Core\Database\Model;

class VacationRepository
{
    public function getById(int $id): ?VacationTransfer
    {
        $vacationModel = static::getVacationModel()::find($id);

        return new VacationTransfer($vacationModel);
    }

    public function getByStatus(?string $status): array
    {
        $vacationModelCollection = static::getVacationModel()::where([
            VacationConstant::STATUS => $status,
        ]);

        $vacationTransferCollection = [];
        foreach ($vacationModelCollection as $vacationModel) {
            $vacationTransferCollection[] = new VacationTransfer($vacationModel);
        }

        return $vacationTransferCollection;
    }

    public function getAllByCriteria(VacationCriteriaTransfer $vacationCriteriaTransfer): array
    {
        $vacationModelCollection = $this->applyFilter(
            static::getVacationModel(),
            $vacationCriteriaTransfer,
            25,
        );

        $vacationTransferCollection = [];
        foreach ($vacationModelCollection as $vacationModel) {
            $vacationTransferCollection[] = new VacationTransfer($vacationModel);
        }

        return $vacationTransferCollection;
    }

    public function updateStatusById(int $vacationId, string $status): VacationTransfer
    {
        $userModel = self::getVacationModel();
        $userModel->id = $vacationId;

        $userModel->update([
            VacationConstant::STATUS => $status,
        ]);

        return new VacationTransfer(self::getVacationModel()::find($vacationId));
    }

    public function getByEmployeeIdByStatus(int $employeeId, string $status): array
    {
        $vacationModelCollection = static::getVacationModel()::where([
            VacationConstant::EMPLOYEE_ID => $employeeId,
            VacationConstant::STATUS => $status,
        ]);

        $vacationTransferCollection = [];
        foreach ($vacationModelCollection as $vacationModel) {
            $vacationTransferCollection[] = new VacationTransfer($vacationModel);
        }

        return $vacationTransferCollection;
    }

    public function create(VacationTransfer $vacationTransfer): VacationTransfer
    {
        $vacationModel = static::getVacationModel()::create($vacationTransfer->toArray(true, false));

        return $vacationTransfer->fromArray($vacationModel);
    }

    public function deleteByCriteria(VacationCriteriaTransfer $vacationCriteriaTransfer): void
    {
        $vacationModelCollection = $this->applyFilter(
            static::getVacationModel(),
            $vacationCriteriaTransfer,
            100,
        );

        foreach ($vacationModelCollection as $vacationModel) {
            static::getVacationModel()::find($vacationModel[CommonConstant::ID]);
        }
    }

    protected function applyFilter(
        Model $model,
        VacationCriteriaTransfer $vacationCriteriaTransfer,
        ?int $limit = 1,
    ): array {
        $wheres = [];
        if ($vacationCriteriaTransfer->getEmployeeId()) {
            $wheres[VacationConstant::EMPLOYEE_ID] = $vacationCriteriaTransfer->getEmployeeId();
        }

        if ($vacationCriteriaTransfer->getStatus()) {
            $wheres[VacationConstant::STATUS] = $vacationCriteriaTransfer->getStatus();
        }

        return $model::orWhere($wheres, $limit);
    }

    private static function getVacationModel(): Vacation
    {
        return new Vacation();
    }
}
