<?php

declare(strict_types=1);

namespace Freyr\Payment\Tests;

use Freyr\Payment\Domain\Transaction\Events\NewTransactionCreated;
use Freyr\Payment\Domain\Transaction\Events\TransactionCancelled;
use Freyr\Payment\Domain\Transaction\Transaction;
use Freyr\Payment\Domain\Transaction\TransactionId;
use Freyr\Payment\Domain\Transaction\TransactionResult;
use Freyr\Payment\Domain\Transaction\TransactionState;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class NewTransactionCreatedTest extends TestCase
{

    #[Test]
    public function shouldSerializeAndDeserialize(): void
    {
        $event = NewTransactionCreated::occur(TransactionId::new(), [
            'state' => TransactionState::CREATED,
            'result' => TransactionResult::PENDING
        ]);
        $serializedEvent = json_encode($event);
        $actualEvent = NewTransactionCreated::fromArray(json_decode($serializedEvent, true));
        self::assertEquals($event->eventId, $actualEvent->eventId);
        self::assertEquals($event->aggregateId, $actualEvent->aggregateId);
        self::assertEquals($event->occurredOn, $actualEvent->occurredOn);
        self::assertEquals($event->state, $actualEvent->state);
        self::assertEquals($event->result, $actualEvent->result);
        self::assertEquals('new-transaction-created', $event->name);
    }

    #[Test]
    public function shouldDeserializeEventStream(): void
    {
        $transaction = Transaction::create(TransactionId::new());
        $transaction->cancel();
        $extractor = fn() => $this->popRecordedEvents();
        $events = $extractor->call($transaction);
        $serializedEvents = json_encode($events);
        $deserializedEvents = json_decode($serializedEvents, true);
        //var_dump($deserializedEvents);die;
        $transaction = Transaction::fromStream($deserializedEvents);
    }
}