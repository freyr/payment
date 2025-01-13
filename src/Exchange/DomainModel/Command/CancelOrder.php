<?php

declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\Command;

use Freyr\Exchange\DomainModel\TradingOrder\OrderId;

interface CancelOrder
{
    public OrderId $orderId { get;}
}
