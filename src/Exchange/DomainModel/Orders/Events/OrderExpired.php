<?php
declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\Orders\Events;

use Freyr\EventSourcing\AggregateChanged;

class OrderExpired extends AggregateChanged
{
    public static function eventName(): string
    {
        return 'exchange.order.expired';
    }

    public function id(): string
    {
        return $this->field('id');
    }

    public function kind(): string
    {
        return $this->field('kind');
    }

    protected static function deserializePayload(array $payload): array
    {
        // TODO: Implement deserializePayload() method.
    }

    protected function serializePayload(array $payload): array
    {
        // TODO: Implement serializePayload() method.
    }
}
