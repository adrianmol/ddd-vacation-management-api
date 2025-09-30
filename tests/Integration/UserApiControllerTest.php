<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use VM\Application\User\Business\UserFacade;
use VM\Application\User\Communication\Controller\Api\UserApiController;
use VM\Application\User\Communication\Request\GetUserRequest;
use VM\Application\User\Communication\Request\StoreUserRequest;
use VM\Application\User\Communication\Request\UpdateUserRequest;
use VM\Application\User\Persistence\Shared\Transfer\UserCriteriaTransfer;
use VM\Application\User\Persistence\Shared\Transfer\UserTransfer;
use VM\Domain\Enums\UserRoleEnum;
use VM\Infrastructure\Http\Constant\HttpConstant;

final class UserApiControllerTest extends TestCase
{
    private function makeRequest(string $class, array $query = [], array $body = [], array $params = [], ?int $userId = null): object
    {
        // Create an anonymous subclass to bypass auth and inject a test user id
        return new class($query, $body, $params, $userId) extends GetUserRequest {
            public function __construct(array $query, array $body, array $params, private ?int $testUserId)
            {
                // Call parent BaseRequest constructor but override needAuth() to false here via this class method
                parent::__construct($query, $body, $params);
            }
            public function needAuth(): bool { return false; }
            public function userId(): ?int { return $this->testUserId; }
        };
    }

    public function testIndexReturnsForbiddenForNonManager(): void
    {
        $userFacade = $this->createMock(UserFacade::class);
        $controller = new UserApiController($userFacade);

        $requester = (new UserTransfer())->setRole(UserRoleEnum::EMPLOYEE->value);
        $userFacade->expects($this->once())
            ->method('getById')
            ->with(100)
            ->willReturn($requester);

        $request = $this->makeRequest(GetUserRequest::class, params: [], userId: 100);

        $response = $controller->index($request);
        $this->assertSame(HttpConstant::STATUS_FORBIDDEN, $response->getStatus());
        $this->assertIsArray($response->getBody());
        $this->assertSame(false === null, isset($response->getBody()['data']));
    }

    public function testIndexReturnsUsersForManager(): void
    {
        $userFacade = $this->createMock(UserFacade::class);
        $controller = new UserApiController($userFacade);

        $manager = (new UserTransfer())->setRole(UserRoleEnum::MANAGER->value);
        $userFacade->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($manager);

        $userFacade->expects($this->once())
            ->method('getAllByRole')
            ->with() // default role
            ->willReturn([new UserTransfer()]);

        $request = $this->makeRequest(GetUserRequest::class, params: [], userId: 1);

        $response = $controller->index($request);
        $this->assertSame(HttpConstant::STATUS_OK, $response->getStatus());
        $this->assertIsArray($response->getBody());
        $this->assertArrayHasKey('data', $response->getBody());
    }

    public function testStoreCreatesUser(): void
    {
        $userFacade = $this->createMock(UserFacade::class);
        $controller = new UserApiController($userFacade);

        $input = [
            'fullName' => 'John Doe',
            'code' => 'ABCDEFGH',
            'email' => 'john@doe.com',
            'username' => 'johnd',
            'password' => 'secret12',
        ];

        $userFacade->expects($this->once())
            ->method('getByCriteria')
            ->with($this->isInstanceOf(UserCriteriaTransfer::class))
            ->willReturn(null);

        $created = (new UserTransfer())->setId(10)->setUsername('johnd');
        $userFacade->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf(UserTransfer::class))
            ->willReturn($created);

        // Use anonymous subclass of StoreUserRequest to bypass auth and provide methods
        $request = new class([], $input, [], 1) extends StoreUserRequest {
            public function __construct(array $query, array $body, array $params, private ?int $testUserId)
            {
                parent::__construct($query, $body, $params);
            }
            public function needAuth(): bool { return false; }
            public function userId(): ?int { return $this->testUserId; }
        };

        $response = $controller->store($request);
        $this->assertSame(HttpConstant::STATUS_CREATED, $response->getStatus());
        $this->assertArrayHasKey('data', $response->getBody());
    }

    public function testUpdateByManagerForbiddenWhenNotManager(): void
    {
        $userFacade = $this->createMock(UserFacade::class);
        $controller = new UserApiController($userFacade);

        $employee = (new UserTransfer())->setRole(UserRoleEnum::EMPLOYEE->value);
        $userFacade->expects($this->once())
            ->method('getById')
            ->with(5)
            ->willReturn($employee);

        $request = new class([], [], [], 5) extends UpdateUserRequest {
            public function __construct(array $query, array $body, array $params, private ?int $testUserId) { parent::__construct($query, $body, $params); }
            public function needAuth(): bool { return false; }
            public function userId(): ?int { return $this->testUserId; }
        };

        $response = $controller->updateByManager(20, $request);
        $this->assertSame(HttpConstant::STATUS_FORBIDDEN, $response->getStatus());
    }

    public function testDeleteByManagerReturnsNoContent(): void
    {
        $userFacade = $this->createMock(UserFacade::class);
        $controller = new UserApiController($userFacade);

        $manager = (new UserTransfer())->setRole(UserRoleEnum::MANAGER->value);
        $userFacade->expects($this->once())
            ->method('getById')
            ->with(2)
            ->willReturn($manager);

        $userFacade->expects($this->once())
            ->method('deleteById')
            ->with(99);

        // BaseRequest is fine for delete, but create a minimal subclass to bypass auth and provide userId
        $request = new class([], [], [], 2) extends GetUserRequest {
            public function __construct(array $query, array $body, array $params, private ?int $testUserId) { parent::__construct($query, $body, $params); }
            public function needAuth(): bool { return false; }
            public function userId(): ?int { return $this->testUserId; }
        };

        $response = $controller->deleteByManager(99, $request);
        $this->assertSame(HttpConstant::STATUS_NO_CONTENT, $response->getStatus());
    }
}
