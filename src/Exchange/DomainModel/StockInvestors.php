<?php

declare(strict_types=1);

namespace Freyr\Exchange\DomainModel;

use Freyr\Exchange\DomainModel\Orders\Order;

class StockInvestors
{
    public int $numberOfShares {
        get => $this->calculateNumberOfShares();
    }
    private array $portfolio = [];

    public function addInvestor(InvestorId $investorId, int $numberOfShares): void
    {
        $this->portfolio[(string) $investorId] = $numberOfShares;
    }

    public static function fromArray(array $stockInvestorData): StockInvestors
    {
        $stockInvestors = new StockInvestors();
        foreach ($stockInvestorData as $investorId => $numberOfShares) {
            $stockInvestors->addInvestor(InvestorId::fromString($investorId), $numberOfShares);
        }

        return $stockInvestors;
    }

    public function toArray(): array
    {
        return $this->portfolio;
    }

    public function moveShares(InvestorId $buyingInvestor, InvestorId $sellingInvestor, int $sharesToExchange): void
    {
        $this->portfolio[(string) $buyingInvestor] = ($this->portfolio[(string) $buyingInvestor] ?? 0) + $sharesToExchange;
        $this->portfolio[(string) $sellingInvestor] -= $sharesToExchange;
    }

    public function canOrderBeFulfilledBySeller(Order $order): bool
    {
        $investorId = $order->investorId;
        $numberOfSharesForSale = $order->numberOfShares;

        return $this->portfolio[(string)$investorId] >= $numberOfSharesForSale;
    }

    private function calculateNumberOfShares(): int
    {
        $totalNumberOfShares = 0;
        foreach ($this->portfolio as $numberOfShares) {
            $totalNumberOfShares += $numberOfShares;
        }
        return $totalNumberOfShares;
    }
}
