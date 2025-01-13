<?php

declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\TradingSession;

use Carbon\Carbon;
use DateTimeImmutable;

readonly class TradingDay
{
    private function __construct(
        private Carbon $date
    )
    {
    }

    public static function create(): self
    {
        return new self(new Carbon('now', 'UTC'));
    }

    public static function fromPrevious(string $previousTradingDay): self
    {
        return new self(Carbon::createFromFormat('Y.m.d', $previousTradingDay, 'UTC'));
    }

    public function __toString(): string
    {
        return $this->date->format('Y.m.d');
    }

    public function sameAs(DateTimeImmutable $day): bool
    {
        return $this->date->isSameDay($day);
    }
}
