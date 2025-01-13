<?php

declare(strict_types=1);

namespace Freyr\QA\DomainModel\TasksCreator\Test;

use Freyr\QA\DomainModel\TasksCreator\PacketTemplate\PacketTemplateId;

readonly class TestConfiguration
{



    public function __construct(
        public PacketTemplateId $packetTemplateId,
        public Tests $tests,
    )
    {

    }
}
