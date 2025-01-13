<?php

declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\Event;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\Exchange\DomainModel\TradingSession\BuyOrders;
use Freyr\Exchange\DomainModel\TradingSession\InactiveOrders;
use Freyr\Exchange\DomainModel\TradingSession\OrderSet;
use Freyr\Exchange\DomainModel\TradingSession\SellOrders;

class TradingSessionWasOpened extends AggregateChanged
{
    public InactiveOrders $inactiveOrders {
        get => $this->payload['inactiveOrders'];
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
