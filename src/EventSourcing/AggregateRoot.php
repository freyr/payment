<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

use DateTimeImmutable;
use Freyr\Identity\Id;

abstract class AggregateRoot
{
    /**
     * @var AggregateChanged[]
     */
    private array $events = [];
    public function __construct(
        readonly public AggregateId $aggregateId,
    ) {}

    public static function fromStream(AggregateId $aggregateId, array $streamEvents): static
    {
        $instance = new static($aggregateId);
        $instance->replay(
            self::deserializeEventStream(
                $aggregateId,
                $streamEvents,
            )
        );
        return $instance;
    }

    /** @var AggregateChanged[] $eventStream */
    protected function replay(array $eventStream): void
    {
        foreach ($eventStream as $event) {
            $this->apply($event);
        }
    }

    protected function recordThat(AggregateChanged $event): void
    {
        $this->events[] = $event;
        $this->apply($event);
    }

    /** @return AggregateChanged[] */
    private static function deserializeEventStream(AggregateId $aggregateId, array $serializedEvents): array
    {
        $events = [];
        foreach ($serializedEvents as $serializedEvent) {
            $eventId = Id::fromString($serializedEvent['eventId']);
            $occurredOn = DateTimeImmutable::createFromTimestamp($serializedEvent['occurredOn']);

            $deserializer = static::eventDeserializer($serializedEvent['name']);
            $events[] = $deserializer(
                $eventId,
                $aggregateId,
                $occurredOn,
                $serializedEvent['payload']
            );
        }

        return $events;
    }

    protected function popEvents(): array
    {
        $pendingEvents = $this->events;
        $this->events = [];
        return $pendingEvents;
    }

    abstract protected function apply(AggregateChanged $event): void;
    abstract static protected function eventDeserializer(string $eventName): callable;
}
