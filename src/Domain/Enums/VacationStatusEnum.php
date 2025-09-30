<?php

declare(strict_types=1);

namespace VM\Domain\Enums;

enum VacationStatusEnum: string
{
    case PENDING = 'pending';

    case APPROVED = 'approved';

    case REJECTED = 'rejected';

    public function canApprove(): bool
    {
        return $this->value === self::PENDING->value;
    }

    public function canReject(): bool
    {
        return $this->value === self::PENDING->value;
    }
}
