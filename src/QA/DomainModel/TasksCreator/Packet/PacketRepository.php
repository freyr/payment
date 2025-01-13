<?php

declare(strict_types=1);

namespace Freyr\QA\DomainModel\TasksCreator\Packet;

interface PacketRepository
{

    public function save(Packet $packet): void;
}
