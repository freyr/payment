<?php
declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\Orders\Events;

use Freyr\EventSourcing\AggregateChanged;

class MarketTransactionExecuted extends AggregateChanged
{

    public static function eventName(): string
    {
        return 'exchange.transaction.executed';
    }

    /**
     * 'sellId' => $sellOrder->id->toRfc4122(),
     * 'buyId' => $buyOrder->id->toRfc4122(),
     * 'numberOfSharesToExchange' => $numberOfSharesToExchange,
     * 'isBuyOrderFulfilled' => $numberOfSharesToExchange === $buyOrder->numberOfShares,
     * 'isSellOrderFulfilled' => $numberOfSharesToExchange === $sellOrder->numberOfShares,
     * 'transactionPrice' => $sellOrder->price
     */

    public function sellId(): string
    {
        return $this->field('sellId');
    }

    public function buyId(): string
    {
        return $this->field('sellId');
    }

    public function buyer(): string
    {
        return $this->field('buyer');
    }

    public function seller(): string
    {
        return $this->field('seller');
    }

    public function exchangeShares(): int
    {
        return $this->field('numberOfSharesToExchange');
    }

    public function marketPrice(): int
    {
        return $this->field('transactionPrice');
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
