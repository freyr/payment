<?php
declare(strict_types=1);

namespace Freyr\Exchange\Commands;

use Freyr\Exchange\DomainModel\StockId;
use Freyr\Exchange\DomainModel\InvestorId;

readonly class AddNewStock
{
    /** @param array<InvestorId, int> $investors */
    public function __construct(
        public array  $investors,
        public StockId $stockId,
        public string $stockName,
        public int    $numberOfShares,
        public int    $price
    )
    {

    }
}
