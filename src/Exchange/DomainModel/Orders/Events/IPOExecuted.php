<?php
declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\Orders\Events;


use Freyr\EventSourcing\AggregateChanged;
use Freyr\Exchange\DomainModel\StockInvestors;

class IPOExecuted extends AggregateChanged
{
    public StockInvestors $portfolio {
        get => $this->payload['portfolio'];
    }

    public int $pricePerShare {
        get => $this->payload['pricePerShare'];
    }
    protected static function deserializePayload(array $payload): array
    {
        return [
            'portfolio' => StockInvestors::fromArray($payload['portfolio']),
        ];
    }

    protected function serializePayload(): array
    {
        return [
            'portfolio' => $this->portfolio->toArray(),
        ];
    }

    static public function eventName(): string
    {
        return 'exchange.ipo.executed';
    }
}
