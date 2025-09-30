<?php

declare(strict_types=1);

namespace VM\Application\User\Business;

use VM\Application\User\Business\Reader\ApiKeyReader;
use VM\Application\User\Persistence\Repository\ApiKeyRepository;

class ApiKeyBusinessFactory
{
    public function createApiKeyReader(): ApiKeyReader
    {
        return new ApiKeyReader(
            $this->getApiKeyRepository()
        );
    }

    protected function getApiKeyRepository(): ApiKeyRepository
    {
        return new ApiKeyRepository();
    }
}
