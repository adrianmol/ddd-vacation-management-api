<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use VM\Application\User\Business\UserBusinessFactory;
use VM\Application\User\Business\UserFacade;
use VM\Application\User\Business\Reader\UserReader;
use VM\Application\User\Persistence\Shared\Transfer\UserCriteriaTransfer;
use VM\Application\User\Persistence\Shared\Transfer\UserTransfer;

final class UserFacadeTest extends TestCase
{
    private function getFacade(): UserFacade
    {
        return new class extends UserFacade {
            public static ?UserBusinessFactory $injectedFactory = null;
            protected static function createFactory(): UserBusinessFactory
            {
                return self::$injectedFactory ?? new UserBusinessFactory();
            }
        };
    }

    public function testLoginDelegatesToReader(): void
    {
        $username = 'john';
        $password = 'secret';
        $expected = new UserTransfer();

        $reader = $this->createMock(UserReader::class);
        $reader->expects($this->once())
            ->method('login')
            ->with($username, $password)
            ->willReturn($expected);

        $factory = $this->createMock(UserBusinessFactory::class);
        $factory->expects($this->once())
            ->method('createUserReader')
            ->willReturn($reader);

        $facade = $this->getFacade();
        $facade::$injectedFactory = $factory;

        $result = $facade->login($username, $password);
        $this->assertSame($expected, $result);
    }

    public function testGetByIdDelegatesToReader(): void
    {
        $id = 42;
        $expected = new UserTransfer();

        $reader = $this->createMock(UserReader::class);
        $reader->expects($this->once())
            ->method('getById')
            ->with($id)
            ->willReturn($expected);

        $factory = $this->createMock(UserBusinessFactory::class);
        $factory->expects($this->once())
            ->method('createUserReader')
            ->willReturn($reader);

        $facade = $this->getFacade();
        $facade::$injectedFactory = $factory;

        $result = $facade->getById($id);
        $this->assertSame($expected, $result);
    }

    public function testGetAllByRoleDelegatesToReader(): void
    {
        $role = 'EMPLOYEE';
        $expected = [new UserTransfer()];

        $reader = $this->createMock(UserReader::class);
        $reader->expects($this->once())
            ->method('getAllByRole')
            ->with($role)
            ->willReturn($expected);

        $factory = $this->createMock(UserBusinessFactory::class);
        $factory->expects($this->once())
            ->method('createUserReader')
            ->willReturn($reader);

        $facade = $this->getFacade();
        $facade::$injectedFactory = $factory;

        $result = $facade->getAllByRole($role);
        $this->assertSame($expected, $result);
    }

    public function testGetByCriteriaDelegatesToReader(): void
    {
        $criteria = new UserCriteriaTransfer();
        $expected = new UserTransfer();

        $reader = $this->createMock(UserReader::class);
        $reader->expects($this->once())
            ->method('getByCriteria')
            ->with($criteria)
            ->willReturn($expected);

        $factory = $this->createMock(UserBusinessFactory::class);
        $factory->expects($this->once())
            ->method('createUserReader')
            ->willReturn($reader);

        $facade = $this->getFacade();
        $facade::$injectedFactory = $factory;

        $result = $facade->getByCriteria($criteria);
        $this->assertSame($expected, $result);
    }

    public function testCreateDelegatesToReader(): void
    {
        $input = new UserTransfer();
        $expected = new UserTransfer();

        $reader = $this->createMock(UserReader::class);
        $reader->expects($this->once())
            ->method('create')
            ->with($input)
            ->willReturn($expected);

        $factory = $this->createMock(UserBusinessFactory::class);
        $factory->expects($this->once())
            ->method('createUserReader')
            ->willReturn($reader);

        $facade = $this->getFacade();
        $facade::$injectedFactory = $factory;

        $result = $facade->create($input);
        $this->assertSame($expected, $result);
    }

    public function testUpdateDelegatesToReader(): void
    {
        $id = 7;
        $input = new UserTransfer();
        $expected = new UserTransfer();

        $reader = $this->createMock(UserReader::class);
        $reader->expects($this->once())
            ->method('update')
            ->with($id, $input)
            ->willReturn($expected);

        $factory = $this->createMock(UserBusinessFactory::class);
        $factory->expects($this->once())
            ->method('createUserReader')
            ->willReturn($reader);

        $facade = $this->getFacade();
        $facade::$injectedFactory = $factory;

        $result = $facade->update($id, $input);
        $this->assertSame($expected, $result);
    }

    public function testDeleteByIdDelegatesToReader(): void
    {
        $id = 9;

        $reader = $this->createMock(UserReader::class);
        $reader->expects($this->once())
            ->method('deleteById')
            ->with($id);

        $factory = $this->createMock(UserBusinessFactory::class);
        $factory->expects($this->once())
            ->method('createUserReader')
            ->willReturn($reader);

        $facade = $this->getFacade();
        $facade::$injectedFactory = $factory;

        $facade->deleteById($id);
        $this->assertTrue(true);
    }
}
