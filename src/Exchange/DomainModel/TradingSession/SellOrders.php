<?php

declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\TradingSession;

use Freyr\Exchange\DomainModel\TradingOrder\Order;

class SellOrders extends OrderSet
{
    public function matchSeller(Order $sellOrder): ?Order
    {

        /** @var Order[] $sellOrders */
        $sellOrders = array_values(
            array_filter(
                $this->orders,
                fn(Order $o) => $o->price <= $sellOrder->price && !$o->walletId->sameAs($sellOrder->walletId)
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
}
