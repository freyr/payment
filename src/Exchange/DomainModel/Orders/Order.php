<?php

declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\Orders;

use Freyr\Exchange\DomainModel\InvestorId;
use Freyr\Exchange\DomainModel\StockId;

readonly class Order
{
    public function __construct(
        public OrderId $id,
        public InvestorId $investorId,
        public StockId $stockId,
        public int $numberOfShares,
        public int $price,
        public Kind $kind,
        public Type $type = Type::LIMIT,
        public Duration $duration = Duration::DAY,
    ) {
    }

    public static function buy(
        OrderId $id,
        InvestorId $investorId,
        StockId $stockId,
        int $numberOfShares,
        int $price,
        Type $type = Type::LIMIT,
        Duration $duration = Duration::DAY
    ): Order {
        return new Order(
            $id,
            $investorId,
            $stockId,
            $numberOfShares,
            $price,
            Kind::BUY,
            $type,
            $duration,
        );
    }

    public static function sell(
        OrderId $id,
        InvestorId $investorId,
        StockId $stockId,
        int $numberOfShares,
        int $price,
        Type $type = Type::LIMIT,
        Duration $duration = Duration::DAY
    ): Order {
        return new Order(
            $id,
            $investorId,
            $stockId,
            $numberOfShares,
            $price,
            Kind::SELL,
            $type,
            $duration
        );
    }

    public function decreaseVolumeBy(int $sharesToExchange): Order
    {
        return new static(
            $this->id,
            $this->investorId,
            $this->stockId,
            $this->numberOfShares - $sharesToExchange,
            $this->price,
            $this->kind,
            $this->type,
            $this->duration
        );
    }
}
