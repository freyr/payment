<?php

declare(strict_types=1);

namespace Freyr\Payment\Domain\Transaction\Events;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\Payment\Domain\Transaction\TransactionResult;
use Freyr\Payment\Domain\Transaction\TransactionState;

class TransactionCancelled extends AggregateChanged
{
    public TransactionState $state {
        get => $this->payload['state'];
    }

    public TransactionResult $result {
        get => $this->payload['result'];
    }

    protected static function deserializePayload(array $payload): array
    {
        return [
            'state' => TransactionState::from($payload['state']),
            'result' => TransactionResult::from($payload['result']),
        ];
    }

    protected function serializePayload(): array
    {
        return [
            'state' => $this->payload['state']->value,
            'result' => $this->payload['result']->value,
        ];
    }

    public static function eventName(): string
    {
        return 'payment.transaction.cancelled';
    }
}
