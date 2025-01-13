<?php
declare(strict_types=1);

namespace Freyr\Exchange\DomainModel;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\EventSourcing\AggregateRoot;
use Freyr\Exchange\DomainModel\Events\NewTradingSessionCreated;
use Freyr\Exchange\DomainModel\Orders\Duration;
use Freyr\Exchange\DomainModel\Orders\Events\BuyOrderRegistered;
use Freyr\Exchange\DomainModel\Orders\Events\IPOExecuted;
use Freyr\Exchange\DomainModel\Orders\Events\MarketTransactionExecuted;
use Freyr\Exchange\DomainModel\Orders\Events\OrderExpired;
use Freyr\Exchange\DomainModel\Orders\Events\OrderFulfilled;
use Freyr\Exchange\DomainModel\Orders\Events\SellOrderRegistered;
use Freyr\Exchange\DomainModel\Orders\Kind;
use Freyr\Exchange\DomainModel\Orders\Order;
use RuntimeException;

class StockTradingSession extends AggregateRoot
{
    public function open(OpenTradingSession $command): void
    {

    }

    public function placeOrder(PlaceOrder $command): void
    {
        if (!$this->isOrderForStock($command)) {
            throw new RuntimeException('Not the same Stock');
        }

        match ($order->kind) {
            Kind::BUY => $this->buy($order),
            Kind::SELL => $this->sell($order),
        };
    }

    public function cancelOrder(CancelOrder $command): void
    {

    }

    public function close(CloseTradingSession $command): void
    {
        foreach ($this->buyOrders as $order) {
            if ($order->duration === Duration::DAY) {
                $event = OrderExpired::occur([
                    'id' => $order->id,
                    'kind' => $order->kind
                ]);
                $this->recordThat($event);
            }
        }

        foreach ($this->sellOrders as $order) {
            if ($order->duration === Duration::DAY) {
                $event = OrderExpired::occur([
                    'id' => $order->id,
                    'kind' => $order->kind,
                ]);
                $this->recordThat($event);
            }
        }
    }

    public static function createNewTradingSession(StockTradingSession $previousTradingSession): self
    {
        $id = TradingSessionId::new();
        $session = new StockTradingSession($id);
        $session->recordThat(
            NewTradingSessionCreated::occur([

            ])
        );
        return $session;
    }

    private function sell(Order $sellOrder): void
    {
        if (!$this->canOrderBeFulfilledBySeller($sellOrder)) {
            throw new RuntimeException('Investor cannot fulfill sell order');
        }
        $this->recordThat(SellOrderRegistered::occur([
            'sellOrderId' => $sellOrder->id,
            'sellerId' => $sellOrder->walletId,
            'numberOfShares' => $sellOrder->numberOfShares,
            'pricePerShare' => $sellOrder->price,
        ]));

        $buyOrder = $this->matchWithBuyer($sellOrder);
        if ($buyOrder) {
            $numberOfSharesToExchange = min($sellOrder->numberOfShares, $buyOrder->numberOfShares);
            $this->recordThat(MarketTransactionExecuted::occur([
                'sellOrderId' => $sellOrder->id,
                'buyOrderId' => $buyOrder->id,
                'numberOfSharesToExchange' => $numberOfSharesToExchange,
                'transactionPrice' => $buyOrder->price,
                'isBuyOrderFulfilled' => $numberOfSharesToExchange === $buyOrder->numberOfShares,
                'isSellOrderFulfilled' => $numberOfSharesToExchange === $sellOrder->numberOfShares,
            ]));

            $this->recordFulfilledStatus($sellOrder, $buyOrder, $numberOfSharesToExchange);
        }
    }

    private function buy(Order $buyOrder): void
    {
        $this->recordThat(BuyOrderRegistered::occur([
            'buyOrderId' => $buyOrder->id,
            'buyerId' => $buyOrder->walletId,
            'numberOfShares' => $buyOrder->numberOfShares,
            'pricePerShare' => $buyOrder->price,
        ]));

        $sellOrder = $this->matchWithSeller($buyOrder);
        if ($sellOrder) {
            $numberOfSharesToExchange = min($sellOrder->numberOfShares, $buyOrder->numberOfShares);
            $this->recordThat(MarketTransactionExecuted::occur([
                'sellOrderId' => $sellOrder->id,
                'buyOrderIdId' => $buyOrder->id,
                'buyerId' => $buyOrder->walletId,
                'sellerId' => $sellOrder->walletId,
                'numberOfSharesToExchange' => $numberOfSharesToExchange,
                'transactionPrice' => $sellOrder->price,
                'isBuyOrderFulfilled' => $numberOfSharesToExchange === $buyOrder->numberOfShares,
                'isSellOrderFulfilled' => $numberOfSharesToExchange === $sellOrder->numberOfShares,
            ]));

            $this->recordFulfilledStatus($sellOrder, $buyOrder, $numberOfSharesToExchange);
        }
    }

    private function recordFulfilledStatus(Order $sellOrder, Order $buyOrder, int $numberOfSharesToExchange): void
    {
        if ($numberOfSharesToExchange === $sellOrder->numberOfShares) {
            $this->recordThat(OrderFulfilled::occur([
                'sellOrderId' => $sellOrder->id,
                'kind' => $sellOrder->kind
            ]));
        }
        if ($numberOfSharesToExchange === $buyOrder->numberOfShares) {
            $this->recordThat(OrderFulfilled::occur([
                'id' => $buyOrder->id,
                'kind' => $buyOrder->kind
            ]));
        }
    }

    private function matchWithBuyer(Order $order): ?Order
    {
        /** @var Order[] $buyOrders */
        $buyOrders = array_values(array_filter($this->buyOrders, fn(Order $o) => $o->price >= $order->price));
        if (!$buyOrders) {
            return null;
        }
        $b = $buyOrders[0];
        foreach ($buyOrders as $o) {
            if ($o->price > $b->price) {
                $b = $o;
            }
        }

        return $b;
    }

    private function matchWithSeller(Order $order): ?Order
    {
        /** @var Order[] $sellOrders */
        $sellOrders = array_values(array_filter($this->sellOrders, fn(Order $o) => $o->price <= $order->price && !$o->walletId->is($order->walletId)));
        if (!$sellOrders) {
            return null;
        }
        $b = $sellOrders[0];
        foreach ($sellOrders as $o) {
            if ($o->price < $b->price) {
                $b = $o;
            }
        }

        return $b;
    }

    private function canOrderBeFulfilledBySeller(Order $order): bool
    {
        $sellerId = $order->walletId;
        $numberOfSharesForSale = $order->numberOfShares;

        return $this->sharePerInvestor[(string) $sellerId] >= $numberOfSharesForSale;
    }

    protected function apply(AggregateChanged $event): void
    {
        match ($event->name) {
            IPOExecuted::eventName() => $this->onIPO($event),
            SellOrderRegistered::eventName() => $this->onSellOrderRegistered($event),
            BuyOrderRegistered::eventName() => $this->onBuyOrderRegistered($event),
            MarketTransactionExecuted::eventName() => $this->onMarketTransactionExecuted($event),
            OrderFulfilled::eventName() => $this->onOrderFulfilled($event),
            OrderExpired::eventName() => $this->onOrderExpired($event),
        };
    }

    static protected function eventDeserializer($eventName): callable
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

    private function isOrderForStock(Order $order): bool
    {
        return $this->symbol === $order->symbol;
    }

    public function sameAs(TradingSessionId $another): bool
    {
        return $this->day->sameAs($another->day);
    }
}
