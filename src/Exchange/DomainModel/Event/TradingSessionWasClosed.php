<?php

declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\Event;

use Freyr\EventSourcing\AggregateChanged;

class TradingSessionWasClosed extends AggregateChanged
{

    protected static function deserializePayload(array $payload): array
    {
        // TODO: Implement deserializePayload() method.
    }

    protected function serializePayload(array $payload): array
    {
        // TODO: Implement serializePayload() method.
    }

    static public function eventName(): string
    {
        // TODO: Implement eventName() method.
    }
}
