<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

use DateTimeImmutable;
use DateTimeZone;
use Freyr\Identity\Id;
use JsonSerializable;

abstract class AggregateChanged implements JsonSerializable
{
    public static function occur(array $payload = []): static
    {
        return new static(
            Id::new(),
            static::eventName(),
            new DateTimeImmutable('now', new DateTimeZone('UTC')),
            $payload
        );
    }

    public function __construct(
        readonly public Id $eventId,
        readonly public string $name,
        readonly public DateTimeImmutable $occurredOn,
        readonly public array $payload
    ) {
    }

    public static function fromArray(array $payload): static
    {
        return new static(
            Id::fromString($payload['_id']),
            static::eventName(),
            new DateTimeImmutable(
                $payload['_occurred_on']['date'],
                new DateTimeZone($payload['_occurred_on']['timezone'])
            ),
            static::deserializePayload(
                array_diff_assoc(
                    $payload,
                    ['_id', '_aggregate_id', '_occurred_on']
                )
            ),
        );
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            [
                '_id' => (string)$this->eventId,
                '_occurred_on' => $this->occurredOn,
                '_name' => $this->name,
            ],
            $this->serializePayload($this->payload)
        );
    }

    abstract protected static function deserializePayload(array $payload): array;
    abstract protected function serializePayload(array $payload): array;
    abstract static public function eventName(): string;
}
