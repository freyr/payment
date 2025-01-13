<?php
declare(strict_types=1);

namespace Freyr\Exchange\Application\Commands;

use Symfony\Component\Uid\Uuid;

readonly class BuyOrder
{
    public function __construct(
        public Uuid $walletId,
        public Uuid $stockId,
        public int  $numberOfShares,
        public int  $price
    )
    {

    }
}
