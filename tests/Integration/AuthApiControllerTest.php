<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use VM\Application\User\Business\UserFacade;
use VM\Application\User\Communication\Controller\Api\AuthApiController;
use VM\Application\User\Communication\Request\LoginRequest;
use VM\Application\User\Persistence\Shared\Transfer\ApiKeyTransfer;
use VM\Application\User\Persistence\Shared\Transfer\UserTransfer;
use VM\Infrastructure\Http\Constant\HttpConstant;

final class AuthApiControllerTest extends TestCase
{
    private function makeLoginRequest(array $body): LoginRequest
    {
        // Anonymous subclass; BaseRequest::needAuth() defaults to false, so no auth is performed
        return new class([], $body, []) extends LoginRequest {
            public function __construct(array $query, array $body, array $params) { parent::__construct($query, $body, $params); }
        };
    }

    public function testIndexReturnsOkOnSuccessfulLogin(): void
    {
        $facade = $this->createMock(UserFacade::class);
        $controller = new AuthApiController($facade);

        $apiKey = (new ApiKeyTransfer())->setApiKey('user|abcdef');
        $user = (new UserTransfer())
            ->setId(1)
            ->setUsername('john')
            ->setApiKeyTransfer($apiKey);

        $facade->expects($this->once())
            ->method('login')
            ->with('john', 'secret12')
            ->willReturn($user);

        $request = $this->makeLoginRequest([
            'username' => 'john',
            'password' => 'secret12',
        ]);

        $response = $controller->index($request);
        $this->assertSame(HttpConstant::STATUS_OK, $response->getStatus());
        $this->assertArrayHasKey('data', $response->getBody());
    }

    public function testIndexReturnsUnauthorizedWhenLoginFails(): void
    {
        $facade = $this->createMock(UserFacade::class);
        $controller = new AuthApiController($facade);

        $facade->expects($this->once())
            ->method('login')
            ->with('john', 'wrong')
            ->willReturn(null);

        $request = $this->makeLoginRequest([
            'username' => 'john',
            'password' => 'wrong',
        ]);

        $response = $controller->index($request);
        $this->assertSame(HttpConstant::STATUS_UNAUTHORIZED, $response->getStatus());
    }
}
