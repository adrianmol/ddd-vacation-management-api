<?php

declare(strict_types=1);

namespace VM\Application\Vacation\Communication\Request;

use VM\Infrastructure\Http\BaseRequest;

class StoreVacationRequest extends BaseRequest
{
    public function validation(): void
    {
        $rules = [
            'startDate' => 'required|date',
            'endDate' => 'required|date',
            'reason' => 'nullable|string',
        ];

        $this->validate($this->body, $rules);
    }

    public function needAuth(): bool
    {
        return true;
    }
}
