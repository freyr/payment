<?php

declare(strict_types=1);

namespace Freyr\Prescription\Issuing\Domain\Prescription;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\Identity\Id;

class PrescriptionWasIssued extends AggregateChanged
{

    protected static function deserializeAggregateId(string $id): Id
    {
        // TODO: Implement deserializeAggregateId() method.
    }

    protected static function deserializePayload(array $payload): array
    {
        // TODO: Implement deserializePayload() method.
    }

    protected function serializePayload(array $payload): array
    {
        // TODO: Implement serializePayload() method.
    }

    static public function eventName(): string
    {
        // TODO: Implement eventName() method.
    }
}