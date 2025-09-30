<?php

declare(strict_types=1);

namespace VM\Application\Vacation\Communication\Controller\Api;

use VM\Application\User\Business\UserFacade;
use VM\Application\Vacation\Business\VacationFacade;
use VM\Application\Vacation\Communication\Request\GetVacationRequest;
use VM\Application\Vacation\Communication\Request\StoreVacationRequest;
use VM\Application\Vacation\Persistence\Mapper\VacationMapper;
use VM\Application\Vacation\Persistence\Shared\Transfer\VacationCriteriaTransfer;
use VM\Application\Vacation\Persistence\Shared\Transfer\VacationTransfer;
use VM\Domain\Enums\VacationStatusEnum;
use VM\Infrastructure\Http\Constant\HttpConstant;
use VM\Infrastructure\Http\Controller\ApiBaseController;
use VM\Infrastructure\Http\Response\JsonResponse;
use VM\Infrastructure\Http\Response\Response;

class VacationApiController extends ApiBaseController
{
    protected const string SUCCESS_MESSAGE = 'Your action has been completed successfully.';

    protected const string FAILED_MESSAGE = 'You cannot update vacation status.';

    public function __construct(
        protected UserFacade $userFacade,
        protected VacationFacade $vacationFacade,
    ) {
    }

    public function index(GetVacationRequest $request): JsonResponse
    {
        $userTransfer = $this->userFacade->getById($request->userId());

        $vacationCriteriaTransfer = (new VacationCriteriaTransfer())
            ->setEmployeeId(
                !$this->isRoleManager($userTransfer?->getRole())
                    ? $request->userId()
                    : null
            )
            ->setStatus($request->getVacationStatus());

        $vacationTransferCollection = $this->vacationFacade->getAllByCriteria($vacationCriteriaTransfer);

        return $this->json(
            VacationMapper::mapVacationCollectionToVacationApiResponseCollection($vacationTransferCollection)
        );
    }

    public function store(StoreVacationRequest $request): JsonResponse
    {
        $request->validation();

        $vacationTransfer = new VacationTransfer($request->body);
        $vacationTransfer
            ->setEmployeeId($request->userId())
            ->setStatus(VacationStatusEnum::PENDING->value);

        $vacationTransfer = $this->vacationFacade->create($vacationTransfer);

        return $this->json(
            VacationMapper::mapVacationToVacationApiResponse($vacationTransfer),
            HttpConstant::STATUS_CREATED
        );
    }

    public function delete(int $vacationId): JsonResponse
    {
    }

    public function action(int $vacationId, string $action, GetVacationRequest $request): JsonResponse
    {
        $userTransfer = $this->userFacade->getById($request->userId());
        if (!$this->isRoleManager($userTransfer?->getRole())) {
            return $this->json()->sendForbidden();
        }

        $updated = $this->vacationFacade->updateStatusById($vacationId, $action);

        return $this->json([
            Response::MESSAGE => $updated
                ? static::SUCCESS_MESSAGE
                : static::FAILED_MESSAGE,
        ],
            $updated
                ? HttpConstant::STATUS_CREATED
                : HttpConstant::STATUS_BAD_REQUEST
        );
    }
}
