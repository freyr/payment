<?php
declare(strict_types=1);

namespace Freyr\Exchange\Application\Commands;

use Freyr\Exchange\DomainModel\TradingSessionId;

readonly class AddNewStock
{
    public function __construct(
        public array  $investors,
        public TradingSessionId $stockId,
        public string $stockName,
        public int    $numberOfShares,
        public int    $price
    )
    {

    }
}
