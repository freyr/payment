<?php

declare(strict_types=1);

namespace Freyr\Payment\Infrastructure;

use DateTimeImmutable;
use Freyr\EventSourcing\AggregateId;
use Freyr\EventSourcing\AggregateRepository;
use Freyr\Identity\Id;
use Freyr\Payment\Domain\Transaction\Transaction;

use function DeepCopy\deep_copy;

class TransactionInMemoryRepository extends AggregateRepository
{

    public array $events = [];

    public function getById(AggregateId $id): Transaction
    {
        return deep_copy($this->events[(string) $id]);
    }
    protected function storeEvents(
        Id $eventId,
        AggregateId $aggregateId,
        DateTimeImmutable $occurredOn,
        string $eventName,
        array $payload
    ): void {
        $this->events[] = [
            'eventId' => (string)$eventId,
            'aggregateId' => $aggregateId->toBinary(),
            'occurredOn' => $occurredOn->getTimestamp(),
            'name' => $eventName,
            'payload' => json_encode($payload),
        ];

    }

    protected function loadEvents(AggregateId $aggregateId): array
    {
        $events = [];
        foreach ($this->events as $event) {
            $id = AggregateId::fromBinary($event['aggregateId']);

            if ($aggregateId->equals($id)) {
                $event['payload'] = json_decode($event['payload'], true);
                $events[] = $event;
            }
        }

        return $events;
    }

    protected function aggregateCallable(): callable
    {
        return Transaction::fromStream(...);
    }
}
