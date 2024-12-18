<?php

declare(strict_types=1);

namespace Freyr\Exchange\DomainModel;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\EventSourcing\AggregateId;
use Freyr\EventSourcing\AggregateRoot;
use Freyr\Exchange\Commands\AddNewStock;
use Freyr\Exchange\DomainModel\Orders\Events\BuyOrderRegistered;
use Freyr\Exchange\DomainModel\Orders\Events\IPOExecuted;
use Freyr\Exchange\DomainModel\Orders\Events\MarketTransactionExecuted;
use Freyr\Exchange\DomainModel\Orders\Events\OrderExpired;
use Freyr\Exchange\DomainModel\Orders\Events\OrderFulfilled;
use Freyr\Exchange\DomainModel\Orders\Events\SellOrderRegistered;
use Freyr\Exchange\DomainModel\Orders\Kind;
use Freyr\Exchange\DomainModel\Orders\Order;
use Freyr\Exchange\DomainModel\Orders\PlacedOrders;
use RuntimeException;

class StockExchange extends AggregateRoot
{
    private StockInvestors $stockInvestors;
    private PlacedOrders $placedOrders;
    private int $marketPrice = 0;

    public function __construct(
        public readonly AggregateId|StockId $aggregateId,
    ) {
        $this->stockInvestors = new StockInvestors();
        $this->placedOrders = new PlacedOrders();
    }

    public function close(): void
    {
        $orders = $this->placedOrders->findOrdersToClose();
        foreach ($orders as $order) {
            $event = OrderExpired::occur($this->aggregateId, [
                'orderId' => $order->id,
                'kind' => $order->kind
            ]);
            $this->recordThat($event);
        }
    }

    public static function executeIPO(AddNewStock $ipo): self
    {
        $ex = new self($ipo->stockId);
        $ex->recordThat(
            IPOExecuted::occur($ipo->stockId, [
                'portfolio' => StockInvestors::fromArray($ipo->investors),
                'pricePerShare' => $ipo->price,
            ])
        );

        return $ex;
    }

    public function placeOrder(Order $order): void
    {
        if (!$order->stockId->equals($this->aggregateId)) {
            throw new RuntimeException('Not the same Stock');
        }

        match ($order->kind) {
            Kind::BUY => $this->buy($order),
            Kind::SELL => $this->sell($order),
        };
    }

    private function sell(Order $sellOrder): void
    {
        if (!$this->stockInvestors->canOrderBeFulfilledBySeller($sellOrder)) {
            throw new RuntimeException('Investor cannot fulfill sell order');
        }
        $this->recordThat(SellOrderRegistered::occur($this->aggregateId, [
            'sellOrderId' => $sellOrder->id,
            'walletId' => $sellOrder->investorId,
            'numberOfShares' => $sellOrder->numberOfShares,
            'price' => $sellOrder->price,
        ]));

        $buyOrder = $this->placedOrders->matchWithBuyer($sellOrder);
        if ($buyOrder) {
            $numberOfSharesToExchange = min($sellOrder->numberOfShares, $buyOrder->numberOfShares);
            $this->recordThat(MarketTransactionExecuted::occur($this->aggregateId, [
                'sellOrderId' => $sellOrder->id,
                'buyOrderId' => $buyOrder->id,
                'numberOfSharesToExchange' => $numberOfSharesToExchange,
                'transactionPrice' => $buyOrder->price
            ]));

            $this->recordFulfilledStatus($sellOrder, $buyOrder, $numberOfSharesToExchange);
        }
    }

    private function buy(Order $buyOrder): void
    {
        $this->recordThat(BuyOrderRegistered::occur($this->aggregateId, [
            'buyOrderId' => $buyOrder->id,
            'walletId' => $buyOrder->investorId,
            'numberOfShares' => $buyOrder->numberOfShares,
            'price' => $buyOrder->price,
        ]));

        $sellOrder = $this->placedOrders->matchWithSeller($buyOrder);
        if ($sellOrder) {
            $numberOfSharesToExchange = min($sellOrder->numberOfShares, $buyOrder->numberOfShares);
            $this->recordThat(MarketTransactionExecuted::occur($this->aggregateId, [
                'sellId' => $sellOrder->id,
                'buyId' => $buyOrder->id,
                'buyer' => $buyOrder->investorId,
                'seller' => $sellOrder->investorId,
                'numberOfSharesToExchange' => $numberOfSharesToExchange,
                'isBuyOrderFulfilled' => $numberOfSharesToExchange === $buyOrder->numberOfShares,
                'isSellOrderFulfilled' => $numberOfSharesToExchange === $sellOrder->numberOfShares,
                'transactionPrice' => $sellOrder->price
            ]));

            $this->recordFulfilledStatus($sellOrder, $buyOrder, $numberOfSharesToExchange);
        }
    }

    private function recordFulfilledStatus(Order $sellOrder, Order $buyOrder, int $numberOfSharesToExchange): void
    {
        if ($numberOfSharesToExchange === $sellOrder->numberOfShares) {
            $this->recordThat(OrderFulfilled::occur($this->aggregateId, [
                'id' => $sellOrder->id,
                'kind' => $sellOrder->kind->value
            ]));
        }
        if ($numberOfSharesToExchange === $buyOrder->numberOfShares) {
            $this->recordThat(OrderFulfilled::occur($this->aggregateId, [
                'id' => $buyOrder->id,
                'kind' => $buyOrder->kind->value
            ]));
        }
    }

    private function onMarketTransactionExecuted(MarketTransactionExecuted $event): void
    {
        $sellOrder = $this->placedOrders->byId($event->sellOrderId);
        $buyOrder = $this->placedOrders->byId($event->buyOrderId);

        $this->stockInvestors->moveShares(
            $buyOrder->investorId,
            $sellOrder->investorId,
            $event->numberOfSharesToExchange,
        );

        $this->placedOrders->adjustOrderVolume($buyOrder, $event->numberOfSharesToExchange);
        $this->placedOrders->adjustOrderVolume($sellOrder, $event->numberOfSharesToExchange);

        $this->marketPrice = $event->transactionPrice;
    }


    public function currentNumberOfSharesOnTheMarket(): int
    {
        return $this->stockInvestors->numberOfShares;
    }

    public function getSharesQuantityOwnedByInvestor(InvestorId $investor): int
    {
        return $this->stockInvestors[(string)$investor] ?? 0;
    }

    public function showSellOrders(): array
    {
        return $this->placedOrders->showSell();
    }

    public function showBuyOrders(): array
    {
        return $this->placedOrders->showBuy();
    }

    public function currentMarketPrice(): int
    {
        return $this->marketPrice;
    }

    public function onIPO(IPOExecuted $event): void
    {
        $this->marketPrice = $event->pricePerShare;
        foreach ($event->portfolio->toArray() as $walletId => $numberOfShares) {
            $this->stockInvestors->addInvestor(InvestorId::fromString($walletId), $numberOfShares);
        }
    }

    private function onSellOrderRegistered(SellOrderRegistered $event): void
    {
        $sellOrder = new Order(
            $event->sellOrderId,
            $event->walletId,
            $this->aggregateId,
            $event->numberOdShares,
            $event->price,
            Kind::SELL
        );
        $this->placedOrders->add($sellOrder);
    }

    private function onBuyOrderRegistered(BuyOrderRegistered $event): void
    {
        $buyOrder = new Order(
            $event->buyOrderId,
            $event->walletId,
            $this->aggregateId,
            $event->numberOfShares,
            $event->price,
            Kind::BUY
        );
        $this->placedOrders->add($buyOrder);
    }

    private function onOrderFulfilled(OrderFulfilled $event): void
    {
        $this->placedOrders->remove($event->orderId);
    }

    private function onOrderExpired(OrderExpired $event): void
    {
        $this->placedOrders->remove($event->orderId);
    }

    protected function apply(AggregateChanged $event): void
    {
        match (get_class($event)) {
            IPOExecuted::class => $this->onIPO($event),
            SellOrderRegistered::class => $this->onSellOrderRegistered($event),
            BuyOrderRegistered::class => $this->onBuyOrderRegistered($event),
            MarketTransactionExecuted::class => $this->onMarketTransactionExecuted($event),
            OrderFulfilled::class => $this->onOrderFulfilled($event),
            OrderExpired::class => $this->onOrderExpired($event),
        };
    }

    static protected function eventDeserializer(string $eventName): callable
    {
        return match ($eventName) {
            IPOExecuted::eventName() => IPOExecuted::fromArray(...),
            SellOrderRegistered::eventName() => SellOrderRegistered::fromArray(...),
            BuyOrderRegistered::eventName() => BuyOrderRegistered::fromArray(...),
            MarketTransactionExecuted::eventName() => MarketTransactionExecuted::fromArray(...),
            OrderFulfilled::eventName() => OrderFulfilled::fromArray(...),
            OrderExpired::eventName() => OrderExpired::fromArray(...),
        };
    }
}
