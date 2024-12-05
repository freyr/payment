<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;
abstract class AggregateRoot
{
    private array $events = [];

    public static function fromStream(array $streamEvents): static
    {
        $instance = new static();
        $instance->replay(self::deserializeEventStream($streamEvents));

        return $instance;
    }

    protected function popRecordedEvents(): array
    {
        $pendingEvents = $this->events;

        $this->events = [];

        return $pendingEvents;
    }

    protected function replay(array $historyEvents): void
    {
        /** @var AggregateChanged $pastEvent */
        foreach ($historyEvents as $pastEvent) {
            $this->apply($pastEvent);
        }
    }

    protected function recordThat(AggregateChanged $event): void
    {
        $this->events[] = $event;
        $this->apply($event);
    }

    abstract protected function apply(AggregateChanged $event): void;

    /**
     * @return AggregateChanged[]
     */
    private static function deserializeEventStream(array $serializedEvents): array
    {
        $events = [];
        foreach ($serializedEvents as $serializedEvent) {
            $eventName = $serializedEvent['_name'];
            $deserializer = static::eventDeserializer($eventName);
            $events[] = $deserializer($serializedEvent);
        }

        return $events;
    }

    abstract static protected function eventDeserializer($eventName): callable;
}