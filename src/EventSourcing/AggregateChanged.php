<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

use DateTimeImmutable;
use DateTimeZone;
use Freyr\Identity\Id;
use JsonSerializable;

abstract class AggregateChanged implements JsonSerializable
{
    public function __construct(
        readonly public Id $eventId,
        readonly public AggregateId $aggregateId,
        readonly public DateTimeImmutable $occurredOn,
        readonly public array $payload
    ) {
    }

    public static function fromArray(
        Id $id,
        AggregateId $aggregateId,
        DateTimeImmutable $occurredOn,
        array $payload
    ): static {
        return new static(
            $id,
            $aggregateId,
            $occurredOn,
            array_merge(
                $payload,
                static::deserializePayload($payload)
            ),
        );
    }

    public static function occur(AggregateId $aggregateId, array $payload = []): static
    {
        return new static(
            Id::new(),
            $aggregateId,
            new DateTimeImmutable('now', new DateTimeZone('UTC')),
            $payload
        );
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            $this->payload,
            $this->serializePayload()
        );
    }

    abstract protected static function deserializePayload(array $payload): array;

    abstract protected function serializePayload(): array;

    abstract static public function eventName(): string;
}
