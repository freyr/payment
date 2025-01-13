<?php

declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\TradingOrder;

use Freyr\Exchange\DomainModel\Command\PlaceOrder;
use Freyr\Exchange\DomainModel\TradingSession\TickerSymbol;

final readonly class Order
{
    public function __construct(
        public OrderId $orderId,
        public TickerSymbol $tickerSymbol,
        public int $numberOfShares,
        public int $price,
        public Kind $kind,
        public Type $type = Type::LIMIT,
        public TradingPeriod $tradingPeriod,
        public Status $status,
    ) {
    }
    public static function fromCommand(PlaceOrder $command): self
    {
        return new Order(
            $command->orderId,
            $command->tickerSymbol,
            $command->numberOfShares,
            $command->price,
            $command->kind,
            $command->type,
            $command->tradingPeriod,
            Status::OPEN,
        );
    }

    public function decreaseVolumeBy(int $sharesToExchange): self
    {
        return new self(
            $this->orderId,
            $this->tickerSymbol,
            $this->numberOfShares - $sharesToExchange,
            $this->price,
            $this->kind,
            $this->type,
            $this->tradingPeriod,
            $this->status,
        );
    }
}
