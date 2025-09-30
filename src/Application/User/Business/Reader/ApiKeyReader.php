<?php

declare(strict_types=1);

namespace VM\Application\User\Business\Reader;

use VM\Application\User\Persistence\Repository\ApiKeyRepository;
use VM\Application\User\Persistence\Shared\Transfer\ApiKeyTransfer;

class ApiKeyReader
{
    public function __construct(
        protected ApiKeyRepository $apiKeyRepository,
    ) {
    }

    public function getByApiKey(string $apiKey): ?ApiKeyTransfer
    {
        return $this->apiKeyRepository->getByApiKey($apiKey);
    }

    public function createApiKey(int $userId): ApiKeyTransfer
    {
        $apiKeyTransfer = new ApiKeyTransfer();

        $apiKeyTransfer
            ->setName('Default Api Key')
            ->setIsActive(1)
            ->setUserId($userId)
            ->setApiKey($userId.'|'.bin2hex(random_bytes(32)));

        return $this->apiKeyRepository->create($apiKeyTransfer);
    }
}
