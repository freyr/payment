<?php
declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\Orders\Events;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\Exchange\DomainModel\Orders\OrderId;
use Freyr\Exchange\DomainModel\InvestorId;

class BuyOrderRegistered extends AggregateChanged
{
    public OrderId $buyOrderId {
        get => $this->payload['buyOrderId'];
    }

    public InvestorId $walletId {
        get => $this->payload['walletId'];
    }

    public int $numberOfShares {
        get => $this->payload['numberOfShares'];
    }

    public int $price {
        get => $this->payload['price'];
    }
    protected static function deserializePayload(array $payload): array
    {
        return [
            'buyOrderId' => OrderId::fromString($payload['buyOrderId']),
            'walletId' => InvestorId::fromString($payload['walletId']),
        ];
    }

    protected function serializePayload(): array
    {
        return [
            'buyOrderId' => (string) $this->buyOrderId,
            'walletId' => (string) $this->walletId,
        ];
    }

    static public function eventName(): string
    {
        return 'exchange.order.buy_registered';
    }
}
