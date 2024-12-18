<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

use DateTimeImmutable;
use Freyr\Identity\Id;

abstract class AggregateRepository
{

    public function load(AggregateId $aggregateId): AggregateRoot
    {
        $results = $this->loadEvents($aggregateId);
        $aggregateCallable = $this->aggregateCallable();
        return $aggregateCallable($aggregateId, $results);
    }

    public function persist(AggregateRoot $aggregate): void
    {
        $events = EventExtractor::extract($aggregate);

        foreach ($events as $event) {
            $this->storeEvents(
                $event->eventId,
                $aggregate->aggregateId,
                $event->occurredOn,
                $event::eventName(),
                $event->payload,
            );
        }
    }

    abstract protected function storeEvents(
        Id $eventId,
        AggregateId $aggregateId,
        DateTimeImmutable $occurredOn,
        string $eventName,
        array $payload
    ): void;

    abstract protected function loadEvents(AggregateId $aggregateId): array;

    /**
     * @return callable (AggregateRoot::fromStream())
     */
    abstract protected function aggregateCallable(): callable;

}
