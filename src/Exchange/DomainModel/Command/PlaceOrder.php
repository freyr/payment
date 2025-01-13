<?php

declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\Command;

use Freyr\Exchange\DomainModel\TradingOrder\Kind;
use Freyr\Exchange\DomainModel\TradingOrder\OrderId;
use Freyr\Exchange\DomainModel\TradingOrder\TradingPeriod;
use Freyr\Exchange\DomainModel\TradingOrder\Type;
use Freyr\Exchange\DomainModel\TradingSession\TickerSymbol;

interface PlaceOrder
{
    public OrderId $orderId {get;}
    public TickerSymbol $tickerSymbol {get;}
    public TradingPeriod $tradingPeriod {get;}
    public Kind $kind { get;}
    public int $numberOfShares {get;}
    public int $price {get;}
    public Type $type {get;}
}
