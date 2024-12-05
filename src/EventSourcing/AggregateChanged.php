<?php
declare(strict_types=1);

namespace Freyr\EventSourcing;

use DateTimeImmutable;
use DateTimeZone;
use Freyr\Identity\Id;
use JsonSerializable;

abstract class AggregateChanged implements JsonSerializable
{
    public static function occur(Id $aggregateId, array $payload = []): static
    {
        $payload['_aggregate_id'] = (string) $aggregateId;
        $payload['_id'] = (string) $aggregateId;
        $payload['_name'] = get_called_class();
        /** @noinspection PhpUnhandledExceptionInspection */
        $occurredOn = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $payload['_occurred_on'] = $occurredOn;

        return new static(
            Id::new(),
            $aggregateId,
            $occurredOn,
            $payload
        );
    }

    public function __construct(
        readonly public Id $eventId,
        readonly public Id $aggregateId,
        readonly public DateTimeImmutable $occurredOn,
        readonly public array $payload
    )
    {
    }

    public static function fromArray(array $payload): static
    {
        $eventId = Id::fromString($payload['_id']);
        $aggregateId = Id::fromString($payload['_aggregate_id']);
        $occurredOn = new DateTimeImmutable($payload['_occurred_on'], new DateTimeZone('UTC'));
        $payload = static::preparePayload($payload);
        return new static($eventId, $aggregateId, $occurredOn, $payload);
    }

    abstract protected static function preparePayload(array $payload): array;
}