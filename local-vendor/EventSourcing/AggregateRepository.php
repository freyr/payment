<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

use Freyr\Identity\Id;

abstract readonly class AggregateRepository
{
    public function __construct(private AggregateStorage $storage)
    {
    }

    public function persist(AggregateRoot $root): void
    {
        $eventExtractor = fn() => $this->popRecordedEvents();
        /** @var AggregateChanged[] $events */
        $events = $eventExtractor->call($root);
        $this->storage->store($root->id, $events);
    }

    public function getById(Id $id): AggregateRoot
    {
        $events = $this->storage->load($id);
        return $this->replayEvents($id, $events);
    }

    abstract protected function replayEvents(Id $id, array $events): AggregateRoot;
}