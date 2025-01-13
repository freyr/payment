<?php

declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\Orders;

use Freyr\Exchange\DomainModel\TradingSessionId;
use Freyr\Exchange\DomainModel\WalletId;
use Freyr\Identity\Id;

readonly class Order
{
    public function __construct(
        public Id $id,
        public WalletId $walletId,
        public TradingSessionId $stockId,
        public int $numberOfShares,
        public int $price,
        public Kind $kind,
        public Type $type = Type::LIMIT,
        public Duration $duration = Duration::DAY
    ) {
    }

    public static function buy(
        Uuid $id,
        WalletId $walletId,
        TradingSessionId $stockId,
        int $numberOfShares,
        int $price,
        Type $type = Type::LIMIT,
        Duration $duration = Duration::DAY
    ): Order {
        return new Order(
            $id, $walletId, $stockId, $numberOfShares, $price, Kind::BUY, $type, $duration
        );
    }

    public static function sell(
        Uuid $id,
        WalletId $walletId,
        TradingSessionId $stockId,
        int $numberOfShares,
        int $price,
        Type $type = Type::LIMIT,
        Duration $duration = Duration::DAY
    ): Order {
        return new Order(
            $id, $walletId, $stockId, $numberOfShares, $price, Kind::SELL, $type, $duration
        );
    }

    public function decreaseVolumeBy(int $sharesToExchange): Order
    {
        return new static(
            $this->id,
            $this->walletId,
            $this->stockId,
            $this->numberOfShares - $sharesToExchange,
            $this->price,
            $this->kind,
            $this->type,
            $this->duration
        );
    }
}
