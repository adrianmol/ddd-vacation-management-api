<?php

declare(strict_types=1);

namespace VM\Application\User\Communication\Request;

use VM\Infrastructure\Http\BaseRequest;

class LoginRequest extends BaseRequest
{
    public function validation(): void
    {
        $rules = [
            'username' => 'required',
            'password' => 'required|string|min:6',
        ];

        $this->validate($this->body, $rules);
    }

    public function getUsername(): string
    {
        return $this->body['username'];
    }

    public function getPassword(): string
    {
        return $this->body['password'];
    }
}
