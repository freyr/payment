<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

class EventExtractor
{
    /**
     * @noinspection PhpUndefinedMethodInspection
     * @return AggregateChanged[]
     */
    public static function extract(AggregateRoot $aggregate): array
    {
        $f = fn(): array => $this->popEvents();
        return $f->call($aggregate);
    }
}
