<?php
declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\Orders\Events;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\Exchange\DomainModel\Orders\Kind;
use Freyr\Exchange\DomainModel\Orders\OrderId;

class OrderExpired extends AggregateChanged
{

    public OrderId $orderId {
        get => $this->payload['orderId'];
    }

    public Kind $kind {
        get => $this->payload['kind'];
    }

    protected static function deserializePayload(array $payload): array
    {
        return [
            'orderId' => OrderId::fromString($payload['orderId']),
            'kind' => Kind::from($payload['kind']),
        ];
    }

    protected function serializePayload(): array
    {
        return [
            'orderId' => (string) $this->orderId,
            'kind' => $this->kind->value,
        ];
    }

    static public function eventName(): string
    {
        return 'exchange.order.expired';
    }
}
