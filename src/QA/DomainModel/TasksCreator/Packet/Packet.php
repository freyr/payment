<?php

declare(strict_types=1);

namespace Freyr\QA\DomainModel\TasksCreator\Packet;

use Freyr\QA\DomainModel\TasksCreator\Creator;
use Freyr\QA\DomainModel\TasksCreator\Environment;
use Freyr\QA\DomainModel\TasksCreator\PacketTemplate\PacketTemplateId;

class Packet
{
    public function __construct(
        PacketId $packetId,
        PacketTemplateId $packetTemplateId,
        Creator $scheduler,
        Environment $environment,
    )
    {
    }
}
