<?php

declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\TradingSession;

use ArrayIterator;
use Carbon\Carbon;
use Freyr\Exchange\DomainModel\TradingOrder\Order;
use Freyr\Exchange\DomainModel\TradingOrder\OrderId;
use Freyr\Exchange\DomainModel\TradingOrder\Status;
use IteratorAggregate;
use Traversable;

class OrderSet implements IteratorAggregate
{

    /** @var Order[] */
    protected array $orders;

    public function __construct(Order ...$orders)
    {
        $this->orders = $orders;
    }

    public function mergeWith(OrderSet $orderSet): static
    {
        array_push($this->orders, ...$orderSet->orders);
        return $this;
    }
    public function findOrdersToClose(): static
    {
        return $this->rawFind(fn(Order $order) => $order->tradingPeriod->isForDay());
    }

    public function findById(OrderId $orderId): static
    {
        return $this->rawFind(fn(Order $order) => $order->orderId->sameAs($orderId));
    }

    public function findForToday(): static
    {
        $today = Carbon::now();
        return $this->rawFind(fn(Order $order) => $today->greaterThanOrEqualTo($order->tradingPeriod->tradingDate));
    }

    public function findOpenGTC(): static
    {
        return $this->rawFind(fn(Order $order) => !$order->tradingPeriod->isForDay() && $order->status === Status::OPEN);
    }

    public function removeById(OrderId $orderId): void
    {
        $order = $this->findById($orderId);
        if ($order->notEmpty()) {
            $filteredItems = array_filter($this->orders, fn(Order $order) => $order->orderId !== $orderId);
            $this->orders = array_values($filteredItems);
        }
    }

    protected function rawFind(callable $callable): static
    {
        $orders = new OrderSet();
        foreach ($this->orders as $order) {
            if ($callable($order)) {
                $orders->add($order);
            }
        }
        return $orders;
    }

    public function add(Order $order): static
    {
        $this->orders[] = $order;
        return $this;
    }

    public function notEmpty(): bool
    {
        return count($this->orders) > 0;
    }

    public function isEmpty(): bool
    {
        return !$this->notEmpty();
    }

    /** @return ArrayIterator|Order[] */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->orders);
    }
}
