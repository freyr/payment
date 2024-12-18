<?php
declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\Orders\Events;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\Exchange\DomainModel\Orders\OrderId;

class MarketTransactionExecuted extends AggregateChanged
{
    public OrderId $sellOrderId {
        get => $this['sellOrderId'];
    }

    public OrderId $buyOrderId {
        get => $this['buyOrderId'];
    }

    public int $numberOfSharesToExchange {
        get => $this['numberOfSharesToExchange'];
    }

    public int $transactionPrice {
        get => $this['transactionPrice'];
    }

    protected static function deserializePayload(array $payload): array
    {
        return [
            'sellOrderId' => OrderId::fromString($payload['sellOrderId']),
            'buyOrderId' => OrderId::fromString($payload['buyOrderId']),
        ];
    }

    protected function serializePayload(): array
    {
        return [
            'sellOrderId' => OrderId::fromString($this->payload['sellOrderId']),
            'buyOrderId' => OrderId::fromString($this->payload['buyOrderId']),
        ];
    }

    static public function eventName(): string
    {
        return 'exchange.transaction.executed';
    }
}
