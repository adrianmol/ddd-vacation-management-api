<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use VM\Application\User\Business\ApiKeyBusinessFactory;
use VM\Application\User\Business\ApiKeyFacade;
use VM\Application\User\Business\Reader\ApiKeyReader;
use VM\Application\User\Persistence\Shared\Transfer\ApiKeyTransfer;

final class ApiKeyFacadeTest extends TestCase
{
    private static ?ApiKeyBusinessFactory $factoryMock = null;

    private function getFacade(): ApiKeyFacade
    {
        return new class extends ApiKeyFacade {
            public static ?ApiKeyBusinessFactory $injectedFactory = null;
            protected static function createFactory(): ApiKeyBusinessFactory
            {
                return self::$injectedFactory ?? new ApiKeyBusinessFactory();
            }
        };
    }

    public function testGetByApiKeyDelegatesToReader(): void
    {
        $apiKey = 'test-api-key';
        $expected = new ApiKeyTransfer();

        $reader = $this->createMock(ApiKeyReader::class);
        $reader->expects($this->once())
            ->method('getByApiKey')
            ->with($apiKey)
            ->willReturn($expected);

        $factory = $this->createMock(ApiKeyBusinessFactory::class);
        $factory->expects($this->once())
            ->method('createApiKeyReader')
            ->willReturn($reader);

        $facade = $this->getFacade();
        $facade::$injectedFactory = $factory;

        $result = $facade->getByApiKey($apiKey);
        $this->assertSame($expected, $result);
    }

    public function testCreateApiKeyDelegatesToReader(): void
    {
        $userId = 123;
        $expected = new ApiKeyTransfer();

        $reader = $this->createMock(ApiKeyReader::class);
        $reader->expects($this->once())
            ->method('createApiKey')
            ->with($userId)
            ->willReturn($expected);

        $factory = $this->createMock(ApiKeyBusinessFactory::class);
        $factory->expects($this->once())
            ->method('createApiKeyReader')
            ->willReturn($reader);

        $facade = $this->getFacade();
        $facade::$injectedFactory = $factory;

        $result = $facade->createApiKey($userId);
        $this->assertSame($expected, $result);
    }
}
