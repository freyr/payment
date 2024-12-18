<?php

declare(strict_types=1);

namespace Freyr\Payment\Tests;

use Freyr\EventSourcing\AggregateId;
use Freyr\EventSourcing\EventExtractor;
use Freyr\Payment\Domain\Transaction\Events\NewTransactionCreated;
use Freyr\Payment\Domain\Transaction\Transaction;
use Freyr\Payment\Domain\Transaction\TransactionId;
use Freyr\Payment\Domain\Transaction\TransactionResult;
use Freyr\Payment\Domain\Transaction\TransactionState;
use Freyr\Payment\Infrastructure\TransactionInMemoryRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class NewTransactionCreatedTest extends TestCase
{

    #[Test]
    public function shouldSerializeAndDeserialize(): void
    {
        $event = NewTransactionCreated::occur(AggregateId::new(), [
            'state' => TransactionState::CREATED,
            'result' => TransactionResult::PENDING
        ]);
        $serializedEvent = json_encode($event);
        $actualEvent = NewTransactionCreated::fromArray(
            $event->eventId,
            $event->aggregateId,
            $event->occurredOn,
            json_decode($serializedEvent, true)
        );
        self::assertEquals($event->eventId, $actualEvent->eventId);
        self::assertEquals($event->aggregateId, $actualEvent->aggregateId);
        self::assertEquals($event->occurredOn, $actualEvent->occurredOn);
        self::assertEquals($event->state, $actualEvent->state);
        self::assertEquals($event->result, $actualEvent->result);
        self::assertEquals('payment.transaction.new', $event::eventName());
    }

    #[Test]
    public function shouldDeserializeEventStream(): void
    {
        $transaction = Transaction::create(TransactionId::new());
        $transaction->cancel();
        $repository = new TransactionInMemoryRepository();

        $repository->persist($transaction);
        $loadTransaction = $repository->load($transaction->aggregateId);

        self::assertEquals($transaction->aggregateId, $loadTransaction->aggregateId);
    }
}
