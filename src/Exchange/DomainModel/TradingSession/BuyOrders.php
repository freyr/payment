<?php

declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\TradingSession;

use Freyr\Exchange\DomainModel\TradingOrder\Order;

class BuyOrders extends OrderSet
{
    public function matchBuyer(Order $sellOrder): ?Order
    {
        $buyOrders = array_values(array_filter($this->orders, fn(Order $o) => $o->price >= $sellOrder->price));
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
}
