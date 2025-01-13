<?php

declare(strict_types=1);

namespace Freyr\QA\DomainModel\TasksCreator\Test;

use Freyr\QA\DomainModel\TasksCreator\PacketTemplate\PacketTemplateId;

interface TestTemplateRepository
{

    public function get(PacketTemplateId $packetTemplateId): TestConfiguration;
}
