<?php

declare(strict_types=1);

namespace VM\Application\Vacation\Business;

use VM\Application\Vacation\Business\Reader\VacationReader;
use VM\Application\Vacation\Persistence\Repository\VacationRepository;

class VacationBusinessFactory
{
    public function createVacationReader(): VacationReader
    {
        return new VacationReader(
            $this->getVacationRepository(),
        );
    }

    protected function getVacationRepository(): VacationRepository
    {
        return new VacationRepository();
    }
}
