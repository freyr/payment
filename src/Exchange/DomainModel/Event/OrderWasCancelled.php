<?php

declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\Event;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\Exchange\DomainModel\TradingOrder\Order;

class OrderWasCancelled extends AggregateChanged
{

    public Order $cancelledOrder {
        get => $this->payload['cancelledOrder'];
    }

    protected static function deserializePayload(array $payload): array
    {
        // TODO: Implement deserializePayload() method.
    }

    protected function serializePayload(array $payload): array
    {
        // TODO: Implement serializePayload() method.
    }

    static public function eventName(): string
    {
        // TODO: Implement eventName() method.
    }
}
