<?php
declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\Event;

use Freyr\EventSourcing\AggregateChanged;

class OrderFulfilled extends AggregateChanged
{

    public static function eventName(): string
    {
        return 'exchange.order.fulfilled';
    }

    protected static function deserializePayload(array $payload): array
    {
        // TODO: Implement deserializePayload() method.
    }

    protected function serializePayload(array $payload): array
    {
        // TODO: Implement serializePayload() method.
    }
}
