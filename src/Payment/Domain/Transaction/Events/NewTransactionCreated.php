<?php

declare(strict_types=1);

namespace Freyr\Payment\Domain\Transaction\Events;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\Identity\Id;
use Freyr\Payment\Domain\Transaction\TransactionId;
use Freyr\Payment\Domain\Transaction\TransactionResult;
use Freyr\Payment\Domain\Transaction\TransactionState;

class NewTransactionCreated extends AggregateChanged
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

    /**
     * @param array{state: TransactionState, result: TransactionResult} $payload
     */
    protected function serializePayload(array $payload): array
    {
        return [
            'state' => $payload['state']->value,
            'result' => $payload['result']->value,
        ];
    }

    public static function eventName(): string
    {
        return 'new-transaction-created';
    }
}
