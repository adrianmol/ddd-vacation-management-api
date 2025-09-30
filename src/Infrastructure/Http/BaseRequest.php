<?php

declare(strict_types=1);

namespace VM\Infrastructure\Http;

use VM\Application\User\Business\ApiKeyFacade;
use VM\Infrastructure\Core\Exception\InvalidArgumentException;
use VM\Infrastructure\Validation\Validator;

class BaseRequest
{
    private ?int $userId = null;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        public array $query,
        public array $body,
        public array $params = [],
    ) {
        $this->needAuth() ? $this->authorize() : null;
    }

    /**
     * @throws \JsonException
     */
    public static function capture(array $params = []): self
    {
        $q = $_GET ?? [];
        $b = json_decode(file_get_contents('php://input') ?: '[]', true, 512, JSON_THROW_ON_ERROR) ?? [];

        return new self($q, $b, $params);
    }

    public function param(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function validate(array &$data, array $rules): array
    {
        $data = array_intersect_key($data, $rules);

        return Validator::validate($data, $rules);
    }

    public function needAuth(): bool
    {
        return false;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function authorize(): bool
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
            throw new InvalidArgumentException(['Missing or invalid Authorization header']);
        }

        $token = trim($matches[1]);

        $apiKeyTransfer = (new ApiKeyFacade())->getByApiKey($token);

        if (!$apiKeyTransfer || !$apiKeyTransfer->getId()) {
            throw new InvalidArgumentException(['Unauthorized: invalid or expired API key']);
        }

        $this->userId = $apiKeyTransfer->getUserId();

        return true;
    }

    public function userId(): ?int
    {
        return $this->userId;
    }
}
