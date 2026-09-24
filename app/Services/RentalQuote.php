<?php

namespace App\Services;

use Carbon\CarbonImmutable;

final class RentalQuote
{
    public function __construct(
        public readonly int $days,
        public readonly string $dailyRate,
        public readonly string $total,
    ) {}

    public static function fromDates(string $dailyRate, string $startsOn, string $endsOn): self
    {
        $start = CarbonImmutable::parse($startsOn)->startOfDay();
        $end = CarbonImmutable::parse($endsOn)->startOfDay();

        if ($end->lessThan($start)) {
            throw new \InvalidArgumentException('Return date must be on or after the pickup date.');
        }

        $days = max(1, (int) $start->diffInDays($end));
        $total = number_format((float) $dailyRate * $days, 2, '.', '');

        return new self($days, number_format((float) $dailyRate, 2, '.', ''), $total);
    }
}
