<?php

declare(strict_types=1);

namespace Freyr\Payment\Domain\Transaction;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\EventSourcing\AggregateId;
use Freyr\EventSourcing\AggregateRoot;
use Freyr\Payment\Domain\Transaction\Events\NewTransactionCreated;
use Freyr\Payment\Domain\Transaction\Events\TransactionCancelled;

class Transaction extends AggregateRoot
{
    private TransactionState $state;
    private TransactionResult $result;

    public function __construct(
        public readonly AggregateId|TransactionId $aggregateId)
    {

    }
    public static function create(TransactionId $id): self
    {
        $transaction = new self($id);
        $transaction->recordThat(
            NewTransactionCreated::occur($transaction->aggregateId, [
                'state' => TransactionState::CREATED,
                'result' => TransactionResult::PENDING,
            ])
        );

        return $transaction;
    }

    public function start(): void
    {
        $this->recordThat(
            NewTransactionCreated::occur($this->aggregateId, [
                'state' => TransactionState::STARTED,
                'result' => TransactionResult::PENDING,
            ])
        );
    }

    public function cancel(): void
    {
        $this->recordThat(
            NewTransactionCreated::occur($this->aggregateId, [
                'state' => TransactionState::CANCELLED,
                'result' => TransactionResult::FAILED,
            ])
        );
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

            }
        }
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

    protected static function eventDeserializer(string $eventName): callable
    {
        return match ($eventName) {
            NewTransactionCreated::eventName() => NewTransactionCreated::fromArray(...),
            TransactionCancelled::eventName() => TransactionCancelled::fromArray(...),
        };
    }
}
