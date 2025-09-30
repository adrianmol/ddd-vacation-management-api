<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use VM\Application\Vacation\Business\VacationBusinessFactory;
use VM\Application\Vacation\Business\VacationFacade;
use VM\Application\Vacation\Business\Reader\VacationReader;
use VM\Application\Vacation\Persistence\Shared\Transfer\VacationCriteriaTransfer;
use VM\Application\Vacation\Persistence\Shared\Transfer\VacationTransfer;

final class VacationFacadeTest extends TestCase
{
    private function getFacade(): VacationFacade
    {
        return new class extends VacationFacade {
            public static ?VacationBusinessFactory $injectedFactory = null;
            protected static function createFactory(): VacationBusinessFactory
            {
                return self::$injectedFactory ?? new VacationBusinessFactory();
            }
        };
    }

    public function testGetByStatusDelegatesToReader(): void
    {
        $status = 'PENDING';
        $expected = [new VacationTransfer()];

        $reader = $this->createMock(VacationReader::class);
        $reader->expects($this->once())
            ->method('getByStatus')
            ->with($status)
            ->willReturn($expected);

        $factory = $this->createMock(VacationBusinessFactory::class);
        $factory->expects($this->once())
            ->method('createVacationReader')
            ->willReturn($reader);

        $facade = $this->getFacade();
        $facade::$injectedFactory = $factory;

        $result = $facade->getByStatus($status);
        $this->assertSame($expected, $result);
    }

    public function testGetAllByCriteriaDelegatesToReader(): void
    {
        $criteria = new VacationCriteriaTransfer();
        $expected = [new VacationTransfer()];

        $reader = $this->createMock(VacationReader::class);
        $reader->expects($this->once())
            ->method('getAllByCriteria')
            ->with($criteria)
            ->willReturn($expected);

        $factory = $this->createMock(VacationBusinessFactory::class);
        $factory->expects($this->once())
            ->method('createVacationReader')
            ->willReturn($reader);

        $facade = $this->getFacade();
        $facade::$injectedFactory = $factory;

        $result = $facade->getAllByCriteria($criteria);
        $this->assertSame($expected, $result);
    }

    public function testGetByEmployeeIdByStatusDelegatesToReader(): void
    {
        $employeeId = 101;
        $status = 'PENDING';
        $expected = [new VacationTransfer()];

        $reader = $this->createMock(VacationReader::class);
        $reader->expects($this->once())
            ->method('getByEmployeeIdByStatus')
            ->with($employeeId, $status)
            ->willReturn($expected);

        $factory = $this->createMock(VacationBusinessFactory::class);
        $factory->expects($this->once())
            ->method('createVacationReader')
            ->willReturn($reader);

        $facade = $this->getFacade();
        $facade::$injectedFactory = $factory;

        $result = $facade->getByEmployeeIdByStatus($employeeId, $status);
        $this->assertSame($expected, $result);
    }

    public function testCreateDelegatesToReader(): void
    {
        $input = new VacationTransfer();
        $expected = new VacationTransfer();

        $reader = $this->createMock(VacationReader::class);
        $reader->expects($this->once())
            ->method('create')
            ->with($input)
            ->willReturn($expected);

        $factory = $this->createMock(VacationBusinessFactory::class);
        $factory->expects($this->once())
            ->method('createVacationReader')
            ->willReturn($reader);

        $facade = $this->getFacade();
        $facade::$injectedFactory = $factory;

        $result = $facade->create($input);
        $this->assertSame($expected, $result);
    }

    public function testUpdateStatusByIdDelegatesToReader(): void
    {
        $id = 55;
        $status = 'APPROVED';
        $expected = true;

        $reader = $this->createMock(VacationReader::class);
        $reader->expects($this->once())
            ->method('updateStatusById')
            ->with($id, $status)
            ->willReturn($expected);

        $factory = $this->createMock(VacationBusinessFactory::class);
        $factory->expects($this->once())
            ->method('createVacationReader')
            ->willReturn($reader);

        $facade = $this->getFacade();
        $facade::$injectedFactory = $factory;

        $result = $facade->updateStatusById($id, $status);
        $this->assertSame($expected, $result);
    }

    public function testDeleteByCriteriaDelegatesToReader(): void
    {
        $criteria = new VacationCriteriaTransfer();

        $reader = $this->createMock(VacationReader::class);
        $reader->expects($this->once())
            ->method('deleteByCriteria')
            ->with($criteria);

        $factory = $this->createMock(VacationBusinessFactory::class);
        $factory->expects($this->once())
            ->method('createVacationReader')
            ->willReturn($reader);

        $facade = $this->getFacade();
        $facade::$injectedFactory = $factory;

        $facade->deleteByCriteria($criteria);
        $this->assertTrue(true);
    }
}
