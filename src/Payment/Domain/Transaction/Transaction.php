<?php

declare(strict_types=1);

namespace Freyr\Payment\Domain\Transaction;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\EventSourcing\AggregateRoot;
use Freyr\Payment\Domain\Transaction\Events\NewTransactionCreated;

class Transaction extends AggregateRoot
{
    private TransactionState $state;
    private TransactionResult $result;

    public static function create(TransactionId $id): self
    {
        $transaction = new self();
        $transaction->recordThat(
            NewTransactionCreated::occur($id, [
                'state' => TransactionState::CREATED,
                'status' => TransactionResult::PENDING,
            ])
        );

        return $transaction;
    }

    public function start(): void
    {

    }

    public function cancel(): void
    {

    }

    public function retry(): void
    {

    }

    public function resolve(TransactionResolution $resolution): void
    {
        if ($resolution->succeeded()) {

        } else {
            if ($this->canBeRetried()) {
                $this->retry();
            } else {
                $this->state = TransactionState::FINALIZED;
                $this->result = TransactionResult::FAILED;
            }
        }
    }

    private function canBeRetried(): bool
    {

    }

    protected function apply(AggregateChanged $event): void
    {
        match (get_class($event)) {
            NewTransactionCreated::class => $this->onNewTransaction($event)
        };
    }

    private function onNewTransaction(NewTransactionCreated $event)
    {
        $this->state = $event->state;
    }
}