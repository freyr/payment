<?php

declare(strict_types=1);

namespace Freyr\QA\DomainModel\TasksCreator;

use Freyr\QA\DomainModel\TasksCreator\Packet\Packet;
use Freyr\QA\DomainModel\TasksCreator\Packet\PacketId;
use Freyr\QA\DomainModel\TasksCreator\Packet\PacketRepository;
use Freyr\QA\DomainModel\TasksCreator\PacketTemplate\PacketTemplateId;
use Freyr\QA\DomainModel\TasksCreator\Test\Test;
use Freyr\QA\DomainModel\TasksCreator\Test\TestRepository;
use Freyr\QA\DomainModel\TasksCreator\Test\TestTemplateRepository;

readonly class QATaskCreator
{

    public function __construct(
        private TestTemplateRepository $testTemplateRepository,
        private PacketRepository $packetRepository,
        private TestRepository $testRepository,
    )
    {

    }
    public function __invoke(
        PacketId $packetId,
        Creator $scheduler,
        Environment $environment,
        PacketTemplateId $packetTemplateId
    ): void
    {
        $packet = new Packet(
            $packetId,
            $packetTemplateId,
            $scheduler,
            $environment,
        );

        $tests = [];
        $testConfiguration = $this->testTemplateRepository->get($packetTemplateId);
        foreach ($testConfiguration->tests->configurations as $configuration) {
            $configuration->setEnvironment($environment);
            $tests[] = new Test(
                $configuration,
                $packetId,
            );
        }

        $this->packetRepository->save($packet);
        $this->testRepository->save(...$tests);
    }
}
