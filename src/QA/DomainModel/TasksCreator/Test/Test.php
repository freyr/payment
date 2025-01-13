<?php

declare(strict_types=1);

namespace Freyr\QA\DomainModel\TasksCreator\Test;


use Freyr\QA\DomainModel\TasksCreator\Packet\PacketId;

class Test
{
    public function __construct(
        Configuration $configuration,
        PacketId $packetId,
    ) {}

}
