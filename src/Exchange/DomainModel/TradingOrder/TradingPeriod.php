<?php

declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\TradingOrder;

use Carbon\Carbon;
use DateTimeImmutable;

final readonly class TradingPeriod
{
    public function __construct(
        public DateTimeImmutable $tradingDate,
        public Duration $duration,
    )
    {
    }

    public function isForDate(DateTimeImmutable $date): bool
    {
        return Carbon::createFromImmutable($this->tradingDate)->greaterThanOrEqualTo($date);
    }

    public function isForDay(): bool
    {
        return $this->duration === Duration::DAY;
    }
}
