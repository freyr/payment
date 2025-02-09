<?php

declare(strict_types=1);

namespace Freyr\Exchange\Application;

use Freyr\Exchange\DomainModel\Command\PlaceOrder;
use Freyr\Exchange\DomainModel\StockTradingService;

class PlaceOrderHandler
{

    private StockTradingService $service;

    public function __invoke(PlaceOrder $command): void
    {
        $result = $this->service->placeOrder($command);
    }
}
