<?php

declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\TradingSession;

use Carbon\Carbon;
use DateTimeImmutable;
use Freyr\EventSourcing\AggregateChanged;
use Freyr\EventSourcing\AggregateRoot;
use Freyr\Exchange\DomainModel\Command\CancelOrder;
use Freyr\Exchange\DomainModel\Command\CloseTradingSession;
use Freyr\Exchange\DomainModel\Command\OpenTradingSession;
use Freyr\Exchange\DomainModel\Command\PlaceOrder;
use Freyr\Exchange\DomainModel\Event\BuyOrderRegistered;
use Freyr\Exchange\DomainModel\Event\InactiveOrderWasRegistered;
use Freyr\Exchange\DomainModel\Event\MarketTransactionExecuted;
use Freyr\Exchange\DomainModel\Event\OrderExpired;
use Freyr\Exchange\DomainModel\Event\OrderFulfilled;
use Freyr\Exchange\DomainModel\Event\OrderWasCancelled;
use Freyr\Exchange\DomainModel\Event\SellOrderRegistered;
use Freyr\Exchange\DomainModel\Event\TradingSessionWasClosed;
use Freyr\Exchange\DomainModel\Event\TradingSessionWasCreated;
use Freyr\Exchange\DomainModel\Event\TradingSessionWasOpened;
use Freyr\Exchange\DomainModel\TradingOrder\Kind;
use Freyr\Exchange\DomainModel\TradingOrder\Order;
use RuntimeException;

final class TradingSession extends AggregateRoot
{
    private SellOrders $sellOrders;
    private BuyOrders $buyOrders;
    private InactiveOrders $inactiveOrders;
    private TradingDay $tradingDay;
    private TickerSymbol $tickerSymbol;

    public static function createNewTradingSession(
        TradingSession $previousTradingSession,
        TradingDay $tradingDay,
    ): self {
        $buyOrders = $previousTradingSession->buyOrders->findOpenGTC();
        $sellOrders = $previousTradingSession->sellOrders->findOpenGTC();
        $session = new TradingSession(TradingSessionId::new());
        $session->recordThat(
            TradingSessionWasCreated::occur([
                'tradingDay' => $tradingDay,
                'buyOrders' => $buyOrders,
                'sellOrders' => $sellOrders,
                'inactiveOrders' => $previousTradingSession->inactiveOrders,
                'tickerSymbol' => $previousTradingSession->tickerSymbol,
            ])
        );
        return $session;
    }

    public function open(OpenTradingSession $command): void
    {
        $activatedOrders = [];
        $inactiveOrders = [];
        foreach ($this->inactiveOrders as $order) {
            if ($order->tradingPeriod->isForDate(new DateTimeImmutable('now', 'UTC'))) {
                $activatedOrders[] = $order;
            } else {
                $inactiveOrders[] = $order;
            }
        }

        $this->recordThat(TradingSessionWasOpened::occur([
            'inactiveOrders' => $inactiveOrders,
        ]));

        foreach ($activatedOrders as $order) {
            match ($order->kind) {
                Kind::BUY => $this->buy($order),
                Kind::SELL => $this->sell($order),
            };
        }
    }

    public function placeOrder(PlaceOrder $command): void
    {
        $order = Order::fromCommand($command);
        if (!$this->isOrderForStock($order)) {
            throw new RuntimeException('Not the same Stock');
        }

        if (!$this->isOrderForToday($order)) {
            $this->recordThat(InactiveOrderWasRegistered::occur([
                'inactiveOrder' => $order,
            ]));
        }

        match ($order->kind) {
            Kind::BUY => $this->buy($order),
            Kind::SELL => $this->sell($order),
        };
    }

    public function cancelOrder(CancelOrder $command): void
    {
        $sellOrder = $this->sellOrders->findById($command->orderId);
        $buyOrder = $this->buyOrders->findById($command->orderId);
        $inactiveOrder = $this->inactiveOrders->findById($command->orderId);

        if ($sellOrder->notEmpty()) {
            $this->recordThat(OrderWasCancelled::occur([
                'cancelledOrder' => $sellOrder,
            ]));
            return;
        }

        if ($buyOrder->notEmpty()) {
            $this->recordThat(OrderWasCancelled::occur([
                'cancelledOrder' => $buyOrder,
            ]));
            return;
        }

        if ($inactiveOrder->notEmpty()) {
            $this->recordThat(OrderWasCancelled::occur([
                'cancelledOrder' => $inactiveOrder,
            ]));
        }
    }

    public function close(CloseTradingSession $command): void
    {
        $ordersToClose = $this->sellOrders->findOrdersToClose();
        $ordersToClose->mergeWith($this->buyOrders->findOrdersToClose());

        foreach ($ordersToClose as $order) {
            $event = OrderExpired::occur([
                'id' => $order->id,
                'kind' => $order->kind,
            ]);
            $this->recordThat($event);
        }

        $this->recordThat(TradingSessionWasClosed::occur([

        ]));
    }

    private function sell(Order $sellOrder): void
    {
        $this->recordThat(SellOrderRegistered::occur([
            'sellOrderId' => $sellOrder->orderId,
            'numberOfShares' => $sellOrder->numberOfShares,
            'pricePerShare' => $sellOrder->price,
        ]));

        $buyOrder = $this->buyOrders->matchBuyer($sellOrder);

        if ($buyOrder) {
            $numberOfSharesToExchange = min($sellOrder->numberOfShares, $buyOrder->numberOfShares);
            $this->recordThat(MarketTransactionExecuted::occur([
                'sellOrderId' => $sellOrder->orderId,
                'buyOrderId' => $buyOrder->orderId,
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
            'buyOrderId' => $buyOrder->orderId,
            'numberOfShares' => $buyOrder->numberOfShares,
            'pricePerShare' => $buyOrder->price,
        ]));

        $sellOrder = $this->sellOrders->matchSeller($buyOrder);

        if ($sellOrder) {
            $numberOfSharesToExchange = min($sellOrder->numberOfShares, $buyOrder->numberOfShares);
            $this->recordThat(MarketTransactionExecuted::occur([
                'sellOrderId' => $sellOrder->orderId,
                'buyOrderIdId' => $buyOrder->orderId,
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
                'sellOrderId' => $sellOrder->orderId,
                'kind' => $sellOrder->kind
            ]));
        }
        if ($numberOfSharesToExchange === $buyOrder->numberOfShares) {
            $this->recordThat(OrderFulfilled::occur([
                'id' => $buyOrder->orderId,
                'kind' => $buyOrder->kind
            ]));
        }
    }

    private function isOrderForStock(Order $order): bool
    {
        return $this->tickerSymbol === $order->tickerSymbol;
    }

    private function isOrderForToday(Order $order): bool
    {
        return $this->tradingDay->sameAs($order->tradingPeriod->tradingDate);
    }

    private function onTradingSessionWasCreated(TradingSessionWasCreated $event): void
    {
        $this->buyOrders = $event->buyOrders;
        $this->sellOrders = $event->sellOrders;
        $this->inactiveOrders = $event->inactiveOrders;
        $this->tickerSymbol = $event->tickerSymbol;
        $this->tradingDay = $event->tradingDay;
    }

    private function onTradingSessionWasOpened(TradingSessionWasOpened $event): void
    {
        $this->inactiveOrders = $event->inactiveOrders;
    }

    private function onInactiveEventWasRegistered(InactiveOrderWasRegistered $event): void
    {
        $this->inactiveOrders->add($event->inactiveOrder);
    }

    private function onOrderWasCancelled(OrderWasCancelled $event): void
    {
        $this->buyOrders->removeById($event->cancelledOrder->orderId);
        $this->sellOrders->removeById($event->cancelledOrder->orderId);
        $this->inactiveOrders->removeById($event->cancelledOrder->orderId);
    }

    private function onTradingSessionWasClosed(AggregateChanged $event): void
    {
    }

    private function onSellOrderRegistered(AggregateChanged $event): void
    {
    }

    private function onBuyOrderRegistered(AggregateChanged $event): void
    {
    }

    private function onMarketTransactionExecuted(AggregateChanged $event): void
    {
    }

    private function onOrderFulfilled(AggregateChanged $event): void
    {
    }

    private function onOrderExpired(AggregateChanged $event): void
    {
    }

    protected function apply(AggregateChanged $event): void
    {
        match (get_class($event)) {
            TradingSessionWasCreated::class => $this->onTradingSessionWasCreated($event),
            InactiveOrderWasRegistered::class => $this->onInactiveEventWasRegistered($event),
            OrderWasCancelled::class => $this->onOrderWasCancelled($event),
            TradingSessionWasOpened::class => $this->onTradingSessionWasOpened($event),
            TradingSessionWasClosed::class => $this->onTradingSessionWasClosed($event),
            SellOrderRegistered::class => $this->onSellOrderRegistered($event),
            BuyOrderRegistered::class => $this->onBuyOrderRegistered($event),
            MarketTransactionExecuted::class => $this->onMarketTransactionExecuted($event),
            OrderFulfilled::class => $this->onOrderFulfilled($event),
            OrderExpired::class => $this->onOrderExpired($event),
        };
    }

    static protected function eventDeserializer($eventName): callable
    {
        return match ($eventName) {
            TradingSessionWasCreated::eventName() => TradingSessionWasCreated::fromArray(...),
            InactiveOrderWasRegistered::eventName() => InactiveOrderWasRegistered::fromArray(...),
            OrderWasCancelled::eventName() => OrderWasCancelled::fromArray(...),
            TradingSessionWasOpened::eventName() => TradingSessionWasOpened::fromArray(...),
            TradingSessionWasClosed::eventName() => TradingSessionWasClosed::fromArray(...),
            SellOrderRegistered::eventName() => SellOrderRegistered::fromArray(...),
            BuyOrderRegistered::eventName() => BuyOrderRegistered::fromArray(...),
            MarketTransactionExecuted::eventName() => MarketTransactionExecuted::fromArray(...),
            OrderFulfilled::eventName() => OrderFulfilled::fromArray(...),
            OrderExpired::eventName() => OrderExpired::fromArray(...),
        };
    }
}
