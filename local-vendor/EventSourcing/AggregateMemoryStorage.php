<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

use Freyr\Identity\Id;

readonly class AggregateMemoryStorage implements AggregateStorage
{

    public array $events;

    public function store(Id $id, array $events): void
    {
        $serializedEvents = array_map('json_encode', $events);
        array_push($this->events[(string) $id], ...$serializedEvents);
    }

    public function load(Id $id): array
    {
        $serializedEvents = $this->events[(string) $id] ?? [];
        return array_map('json_decode', $serializedEvents, true);
    }


}