<?php
declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\Orders\Events;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\Exchange\DomainModel\Orders\OrderId;
use Freyr\Exchange\DomainModel\InvestorId;

class SellOrderRegistered extends AggregateChanged
{
    public OrderId $sellOrderId {
        get => $this->payload['sellOrderId'];
    }

    public InvestorId $walletId {
        get => $this->payload['walletId'];
    }

    public int $numberOdShares {
        get => $this->payload['numberOdShares'];
    }

    public int $price {
        get => $this->payload['price'];
    }

    protected static function deserializePayload(array $payload): array
    {
        return [
            'sellOrderId' => OrderId::fromString($payload['sellOrderId']),
            'walletId' => InvestorId::fromString($payload['walletId']),
        ];
    }

    protected function serializePayload(): array
    {
        return [
            'sellOrderId' => (string) $this->sellOrderId,
            'walletId' => (string) $this->walletId,
        ];
    }

    static public function eventName(): string
    {
        return 'exchange.order.sell_registered';
    }
}
