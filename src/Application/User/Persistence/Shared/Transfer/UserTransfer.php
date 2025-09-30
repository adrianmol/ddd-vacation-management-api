<?php

declare(strict_types=1);

namespace VM\Application\User\Persistence\Shared\Transfer;

use VM\Infrastructure\Transfer\AbstractTransfer;

class UserTransfer extends AbstractTransfer
{
    public ?int $id;
    public ?string $username;
    public ?string $email;
    public ?string $fullName;

    public ?string $code;
    public ?string $password;
    public ?string $role;

    public ?ApiKeyTransfer $apiKeyTransfer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getApiKeyTransfer(): ?ApiKeyTransfer
    {
        return $this->apiKeyTransfer;
    }

    public function setApiKeyTransfer(?ApiKeyTransfer $apiKeyTransfer): self
    {
        $this->apiKeyTransfer = $apiKeyTransfer;

        return $this;
    }
}
