<?php

declare(strict_types=1);

namespace Freyr\Payment\Domain\Transaction;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\EventSourcing\AggregateRoot;
use Freyr\Payment\Domain\Transaction\Events\NewTransactionCreated;
use Freyr\Payment\Domain\Transaction\Events\TransactionCancelled;

class Transaction extends AggregateRoot
{
    private TransactionState $state;
    private TransactionResult $result;

    public static function create(TransactionId $id): self
    {
        $transaction = new self($id);
        $transaction->recordThat(
            NewTransactionCreated::occur([
                'state' => TransactionState::CREATED,
                'result' => TransactionResult::PENDING,
            ])
        );

        return $transaction;
    }

    public function start(): void
    {
        $this->recordThat(
            NewTransactionCreated::occur([
                'state' => TransactionState::STARTED,
                'result' => TransactionResult::PENDING,
            ])
        );
    }

    public function cancel(): void
    {
        $this->recordThat(
            NewTransactionCreated::occur([
                'state' => TransactionState::CANCELLED,
                'result' => TransactionResult::FAILED,
            ])
        );
    }

    public function resolve(TransactionResolution $resolution): void
    {

    }

    private function canBeRetried(): bool
    {
        return false;
    }

    protected function apply(AggregateChanged $event): void
    {
        match (get_class($event)) {
            NewTransactionCreated::class => $this->onNewTransaction($event),
            TransactionCancelled::class => $this->onTransactionCancelled($event),
        };
    }

    private function onNewTransaction(NewTransactionCreated $event): void
    {
        $this->state = $event->state;
        $this->result = $event->result;
    }

    private function onTransactionCancelled(TransactionCancelled $event): void
    {
        $this->state = $event->state;
        $this->result = $event->result;
    }

    protected static function eventDeserializer($eventName): callable
    {
        return match ($eventName) {
            NewTransactionCreated::eventName() => NewTransactionCreated::fromArray(...),
            TransactionCancelled::eventName() => TransactionCancelled::fromArray(...),
        };
    }
}
