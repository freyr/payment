<?php

declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\Orders;

class PlacedOrders
{

    private array $buy = [];
    private array $sell = [];

    public function add(Order $order): void
    {
        if ($order->kind === Kind::BUY) {
            $this->buy[(string) $order->id] = $order;
        } else {
            $this->sell[(string) $order->id] = $order;
        }

    }

    /**
     * @return Order[]
     */
    public function findOrdersToClose(): array
    {
        $orders = [];
        foreach ($this->buy as $order) {
            if ($order->duration === Duration::DAY) {
                $orders[] = $order;
            }
        }

        foreach ($this->sell as $order) {
            if ($order->duration === Duration::DAY) {
                $orders[] = $order;
            }
        }

        return $orders;
    }

    public function matchWithBuyer(Order $sellOrder): ?Order
    {
        /** @var Order[] $buyOrders */
        $buyOrders = array_values(
            array_filter(
                $this->buy,
                fn(Order $o) => $o->price >= $sellOrder->price
            )
        );

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

    public function matchWithSeller(Order $buyOrder): ?Order
    {
        /** @var Order[] $sellOrders */
        $sellOrders = array_values(
            array_filter(
                $this->sell,
                fn(Order $o) => $o->price <= $buyOrder->price &&
                    !$o->investorId->equals($buyOrder->investorId)
            )
        );

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

    public function byId(OrderId $orderId): Order
    {
        return $this->sell[(string) $orderId] ?? $this->buy[(string) $orderId];
    }

    public function adjustOrderVolume(Order $order, int $sharesToExchange): void
    {
        if ($order->kind === Kind::BUY) {
            $this->buy[(string) $order->id] = $order->decreaseVolumeBy($sharesToExchange);
        }

        if ($order->kind === Kind::SELL) {
            $this->sell[(string) $order->id] = $order->decreaseVolumeBy($sharesToExchange);
        }
    }

    public function remove(OrderId $orderId): void
    {
        $order = $this->byId($orderId);
        if ($order->kind === Kind::BUY) {
            unset($this->buy[(string)$order->id]);
        }

        if ($order->kind === Kind::SELL) {
            unset($this->sell[(string)$order->id]);
        }

    }

    public function showSell(): array
    {
        return $this->sell;
    }

    public function showBuy(): array
    {
        return $this->buy;
    }
}
