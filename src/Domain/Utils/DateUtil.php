<?php

declare(strict_types=1);

namespace VM\Domain\Utils;

trait DateUtil
{
    protected const string DATE_FORMAT = 'Y-m-d';

    protected const string DATETIME_FORMAT = 'Y-m-d H:i:s';

    public function now(string $format = self::DATETIME_FORMAT): string
    {
        return (new \DateTime())->format($format);
    }

    public function today(string $format = self::DATE_FORMAT): string
    {
        return (new \DateTime('today'))->format($format);
    }

    public function parseDate(string $date, string $format = self::DATE_FORMAT): ?\DateTime
    {
        $d = \DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) === $date ? $d : null;
    }

    public function isValidDate(string $date, string $format = self::DATE_FORMAT): bool
    {
        return null !== $this->parseDate($date, $format);
    }

    public function compareDates(string $a, string $b, string $format = self::DATE_FORMAT): int
    {
        $dateA = $this->parseDate($a, $format);
        $dateB = $this->parseDate($b, $format);

        if (!$dateA || !$dateB) {
            return 0;
        }

        return $dateA <=> $dateB;
    }
}
