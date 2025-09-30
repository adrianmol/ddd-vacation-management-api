<?php

declare(strict_types=1);

namespace VM\Application\User\Communication\Request;

use VM\Infrastructure\Http\BaseRequest;

class GetUserRequest extends BaseRequest
{
    public function needAuth(): bool
    {
        return true;
    }
}
