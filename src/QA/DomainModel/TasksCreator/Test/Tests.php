<?php

declare(strict_types=1);

namespace Freyr\QA\DomainModel\TasksCreator\Test;

readonly class Tests
{
    /** @var TestConfiguration[] */
    public array $configurations;

    public function __construct(
        array $configurations
    )
    {
        $this->configurations = $configurations;
    }
}
