<?php

declare(strict_types=1);

namespace VM\Application\User\Communication\Request;

use VM\Infrastructure\Http\BaseRequest;

class UpdateUserRequest extends BaseRequest
{
    public function validation(): void
    {
        $rules = [
            'fullName' => 'nullable',
            'email' => 'nullable|email',
            'username' => 'nullable',
            'password' => 'nullable|string|min:6',
        ];

        $this->validate($this->body, $rules);
    }

    public function needAuth(): bool
    {
        return true;
    }
}
