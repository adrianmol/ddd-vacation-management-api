<?php

declare(strict_types=1);

namespace VM\Application\User\Business;

use VM\Application\User\Business\Reader\UserReader;
use VM\Application\User\Persistence\Repository\UserRepository;
use VM\Application\Vacation\Business\VacationFacade;

class UserBusinessFactory
{
    public function createUserReader(): UserReader
    {
        return new UserReader(
            $this->getUserRepository(),
            $this->getApiKeyFacade(),
            $this->getVacationFacade(),
        );
    }

    protected function getUserRepository(): UserRepository
    {
        return new UserRepository();
    }

    protected function getApiKeyFacade(): ApiKeyFacade
    {
        return new ApiKeyFacade();
    }

    protected function getVacationFacade(): VacationFacade
    {
        return new VacationFacade();
    }
}
