<?php

declare(strict_types=1);

namespace Freyr\EventSourcing;

use Freyr\Identity\Id;

interface AggregateStorage
{
    public function store(Id $id, array $events): void;

    /**
     * @param Id $id
     * @return array<int, AggregateChanged>
     */
    public function load(Id $id): array;
}