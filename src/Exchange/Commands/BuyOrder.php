<?php
declare(strict_types=1);

namespace Freyr\Exchange\Commands;

use Freyr\Identity\Id;

readonly class BuyOrder
{
    public function __construct(
        public Id $walletId,
        public Id $stockId,
        public int  $numberOfShares,
        public int  $price
    )
    {

    }
}
