<?php

declare(strict_types=1);

namespace VM\Application\User\Persistence\Repository;

use VM\Application\User\Persistence\Model\ApiKey;
use VM\Application\User\Persistence\Shared\Constant\ApiKeyConstant;
use VM\Application\User\Persistence\Shared\Transfer\ApiKeyTransfer;
use VM\Infrastructure\Core\Constant\CommonConstant;

class ApiKeyRepository
{
    public function getByApiKey(string $apiKey): ?ApiKeyTransfer
    {
        $userId = explode('|', $apiKey)[0] ?? null;

        $apiKeyModel = self::getApiKeyModel()->where([
            CommonConstant::USER_ID => $userId,
            ApiKeyConstant::API_KEY => $apiKey,
        ])[0] ?? null;

        if (null === $apiKeyModel) {
            return null;
        }

        return new ApiKeyTransfer($apiKeyModel);
    }

    public function create(ApiKeyTransfer $apiKeyTransfer): ApiKeyTransfer
    {
        $apiKeyModel = self::getApiKeyModel()::create($apiKeyTransfer->toArray(true, false));

        return $apiKeyTransfer->fromArray($apiKeyModel);
    }

    private static function getApiKeyModel(): ApiKey
    {
        return new ApiKey();
    }
}
