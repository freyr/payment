<?php
declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\Orders\Events;

use Freyr\EventSourcing\AggregateChanged;

class SellOrderRegistered extends AggregateChanged
{
    public static function eventName(): string
    {
        return 'exchange.order.sell.registered';
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
