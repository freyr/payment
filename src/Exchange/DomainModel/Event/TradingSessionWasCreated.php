<?php

declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\Event;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\Exchange\DomainModel\TradingSession\BuyOrders;
use Freyr\Exchange\DomainModel\TradingSession\InactiveOrders;
use Freyr\Exchange\DomainModel\TradingSession\SellOrders;
use Freyr\Exchange\DomainModel\TradingSession\TickerSymbol;
use Freyr\Exchange\DomainModel\TradingSession\TradingDay;

class TradingSessionWasCreated extends AggregateChanged
{

    public BuyOrders $buyOrders {
        get => $this->payload['buyOrders'];
    }
    public SellOrders $sellOrders {
        get => $this->payload['sellOrders'];
    }
    public InactiveOrders $inactiveOrders {
        get => $this->payload['inactiveOrders'];
    }

    public TickerSymbol $tickerSymbol {
        get => $this->payload['tickerSymbol'];
    }

    public TradingDay $tradingDay {
        get => $this->payload['tradingDay'];
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
