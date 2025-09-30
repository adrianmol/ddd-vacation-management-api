<?php

declare(strict_types=1);

namespace VM\Application\Vacation\Communication\Request;

use VM\Application\Vacation\Persistence\Shared\Constant\VacationApiConstant;
use VM\Domain\Enums\VacationStatusEnum;
use VM\Infrastructure\Http\BaseRequest;

class GetVacationRequest extends BaseRequest
{
    public function needAuth(): bool
    {
        return true;
    }

    public function getVacationStatus(): ?string
    {
        return VacationStatusEnum::tryFrom($this->query[VacationApiConstant::STATUS] ?? '')
            ->value ?? null;
    }
}
