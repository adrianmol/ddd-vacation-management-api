<?php

declare(strict_types=1);

namespace VM\Application\User\Business;

use VM\Application\User\Persistence\Shared\Transfer\ApiKeyTransfer;
use VM\Infrastructure\Facade\AbstractFacade;

/**
 * @method ApiKeyBusinessFactory getFactory()
 */
class ApiKeyFacade extends AbstractFacade
{
    public function getByApiKey(string $apiKey): ?ApiKeyTransfer
    {
        return $this->getFactory()
            ->createApiKeyReader()
            ->getByApiKey($apiKey);
    }

    public function createApiKey(int $userId): ApiKeyTransfer
    {
        return $this->getFactory()
            ->createApiKeyReader()
            ->createApiKey($userId);
    }

    protected static function createFactory(): ApiKeyBusinessFactory
    {
        return new ApiKeyBusinessFactory();
    }
}
