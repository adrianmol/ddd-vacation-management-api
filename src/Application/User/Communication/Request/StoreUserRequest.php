<?php

declare(strict_types=1);

namespace VM\Application\User\Communication\Request;

use VM\Infrastructure\Http\BaseRequest;

class StoreUserRequest extends BaseRequest
{
    public function validation(): void
    {
        $rules = [
            'fullName' => 'required',
            'code' => 'required|min:8|max:8',
            'email' => 'required|email',
            'username' => 'required',
            'password' => 'required|string|min:6',
        ];

        $this->validate($this->body, $rules);
    }
}
