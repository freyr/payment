<?php

declare(strict_types=1);

namespace Freyr\Payment\Domain\Transaction\Events;

use Freyr\EventSourcing\AggregateChanged;
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

    protected static function preparePayload(array $payload): array
    {
        return [
            'state' => TransactionState::from($payload['state']),
            'result' => TransactionResult::from($payload['result']),
        ];
    }

    public function jsonSerialize(): mixed
    {

    }
}