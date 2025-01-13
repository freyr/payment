<?php
declare(strict_types=1);

namespace Freyr\Exchange\Infrastructure;

use Freyr\EventSourcing\AggregateRepository;
use Freyr\EventSourcing\AggregateRoot;
use Freyr\Identity\Id;

readonly class StockExchangeRepository extends AggregateRepository
{
    protected function replayEvents(Id $id, array $events): AggregateRoot
    {
        // TODO: Implement replayEvents() method.
    }
}
