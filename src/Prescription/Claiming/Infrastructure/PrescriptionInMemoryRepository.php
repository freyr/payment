<?php

declare(strict_types=1);

namespace Freyr\Prescription\Claiming\Infrastructure;

use Freyr\EventSourcing\AggregateMemoryStorage;
use Freyr\EventSourcing\AggregateRepository;
use Freyr\Identity\Id;
use Freyr\Prescription\Claiming\Domain\Prescription\Prescription;

readonly class PrescriptionInMemoryRepository extends AggregateRepository
{
    public function __construct(AggregateMemoryStorage $storage)
    {
        parent::__construct($storage);
    }

    protected function replayEvents(Id $id, array $events): Prescription
    {
        return Prescription::fromStream($id, $events);
    }
}