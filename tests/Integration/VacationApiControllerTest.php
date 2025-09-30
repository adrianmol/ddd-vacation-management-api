<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use VM\Application\User\Business\UserFacade;
use VM\Application\User\Persistence\Shared\Transfer\UserTransfer;
use VM\Application\Vacation\Business\VacationFacade;
use VM\Application\Vacation\Communication\Controller\Api\VacationApiController;
use VM\Application\Vacation\Communication\Request\GetVacationRequest;
use VM\Application\Vacation\Communication\Request\StoreVacationRequest;
use VM\Application\Vacation\Persistence\Shared\Transfer\VacationCriteriaTransfer;
use VM\Application\Vacation\Persistence\Shared\Transfer\VacationTransfer;
use VM\Domain\Enums\UserRoleEnum;
use VM\Infrastructure\Http\Constant\HttpConstant;
use VM\Infrastructure\Http\Response\Response;

final class VacationApiControllerTest extends TestCase
{
    private function makeGetRequest(array $query = [], array $params = [], ?int $userId = null): GetVacationRequest
    {
        return new class($query, [], $params, $userId) extends GetVacationRequest {
            public function __construct(array $query, array $body, array $params, private ?int $testUserId) { parent::__construct($query, $body, $params); }
            public function needAuth(): bool { return false; }
            public function userId(): ?int { return $this->testUserId; }
        };
    }

    private function makeStoreRequest(array $body = [], ?int $userId = null): StoreVacationRequest
    {
        return new class([], $body, [], $userId) extends StoreVacationRequest {
            public function __construct(array $query, array $body, array $params, private ?int $testUserId) { parent::__construct($query, $body, $params); }
            public function needAuth(): bool { return false; }
            public function userId(): ?int { return $this->testUserId; }
        };
    }

    public function testIndexFiltersByEmployeeWhenNotManager(): void
    {
        $userFacade = $this->createMock(UserFacade::class);
        $vacationFacade = $this->createMock(VacationFacade::class);
        $controller = new VacationApiController($userFacade, $vacationFacade);

        $employee = (new UserTransfer())->setRole(UserRoleEnum::EMPLOYEE->value);
        $userFacade->expects($this->once())
            ->method('getById')
            ->with(10)
            ->willReturn($employee);

        $vacationFacade->expects($this->once())
            ->method('getAllByCriteria')
            ->with($this->callback(function (VacationCriteriaTransfer $c) {
                // when not manager, employeeId must be set to request user id
                return $c->getEmployeeId() === 10;
            }))
            ->willReturn([new VacationTransfer()]);

        $request = $this->makeGetRequest(['status' => 'PENDING'], userId: 10);
        $response = $controller->index($request);

        $this->assertSame(HttpConstant::STATUS_OK, $response->getStatus());
        $this->assertArrayHasKey('data', $response->getBody());
    }

    public function testIndexAllowsManagerToSeeAll(): void
    {
        $userFacade = $this->createMock(UserFacade::class);
        $vacationFacade = $this->createMock(VacationFacade::class);
        $controller = new VacationApiController($userFacade, $vacationFacade);

        $manager = (new UserTransfer())->setRole(UserRoleEnum::MANAGER->value);
        $userFacade->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($manager);

        $vacationFacade->expects($this->once())
            ->method('getAllByCriteria')
            ->with($this->callback(function (VacationCriteriaTransfer $c) {
                // for manager, employeeId should be null
                return $c->getEmployeeId() === null;
            }))
            ->willReturn([new VacationTransfer()]);

        $request = $this->makeGetRequest(['status' => 'APPROVED'], userId: 1);
        $response = $controller->index($request);
        $this->assertSame(HttpConstant::STATUS_OK, $response->getStatus());
    }

    public function testStoreCreatesVacation(): void
    {
        $userFacade = $this->createMock(UserFacade::class);
        $vacationFacade = $this->createMock(VacationFacade::class);
        $controller = new VacationApiController($userFacade, $vacationFacade);

        $input = [
            'startDate' => '2025-10-01',
            'endDate' => '2025-10-05',
            'reason' => 'Family trip',
        ];

        $vacationFacade->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf(VacationTransfer::class))
            ->willReturn((new VacationTransfer())->setId(77));

        $request = $this->makeStoreRequest($input, userId: 22);
        $response = $controller->store($request);

        $this->assertSame(HttpConstant::STATUS_CREATED, $response->getStatus());
        $this->assertArrayHasKey('data', $response->getBody());
    }

    public function testActionForbiddenForNonManager(): void
    {
        $userFacade = $this->createMock(UserFacade::class);
        $vacationFacade = $this->createMock(VacationFacade::class);
        $controller = new VacationApiController($userFacade, $vacationFacade);

        $employee = (new UserTransfer())->setRole(UserRoleEnum::EMPLOYEE->value);
        $userFacade->expects($this->once())
            ->method('getById')
            ->with(5)
            ->willReturn($employee);

        $request = $this->makeGetRequest([], userId: 5);
        $response = $controller->action(100, 'APPROVED', $request);

        $this->assertSame(HttpConstant::STATUS_FORBIDDEN, $response->getStatus());
        $this->assertArrayHasKey(Response::CODE, $response->getBody());
    }

    public function testActionApproveReturnsCreatedOnSuccess(): void
    {
        $userFacade = $this->createMock(UserFacade::class);
        $vacationFacade = $this->createMock(VacationFacade::class);
        $controller = new VacationApiController($userFacade, $vacationFacade);

        $manager = (new UserTransfer())->setRole(UserRoleEnum::MANAGER->value);
        $userFacade->expects($this->once())
            ->method('getById')
            ->with(3)
            ->willReturn($manager);

        $vacationFacade->expects($this->once())
            ->method('updateStatusById')
            ->with(200, 'APPROVED')
            ->willReturn(true);

        $request = $this->makeGetRequest([], userId: 3);
        $response = $controller->action(200, 'APPROVED', $request);

        $this->assertSame(HttpConstant::STATUS_CREATED, $response->getStatus());
        $this->assertEquals('Your action has been completed successfully.', $response->getBody()['data']['message']);
    }

    public function testActionApproveReturnsBadRequestOnFailure(): void
    {
        $userFacade = $this->createMock(UserFacade::class);
        $vacationFacade = $this->createMock(VacationFacade::class);
        $controller = new VacationApiController($userFacade, $vacationFacade);

        $manager = (new UserTransfer())->setRole(UserRoleEnum::MANAGER->value);
        $userFacade->expects($this->once())
            ->method('getById')
            ->with(4)
            ->willReturn($manager);

        $vacationFacade->expects($this->once())
            ->method('updateStatusById')
            ->with(201, 'REJECTED')
            ->willReturn(false);

        $request = $this->makeGetRequest([], userId: 4);
        $response = $controller->action(201, 'REJECTED', $request);

        $this->assertSame(HttpConstant::STATUS_BAD_REQUEST, $response->getStatus());
        $this->assertEquals('You cannot update vacation status.', $response->getBody()['data']['message']);
    }
}
