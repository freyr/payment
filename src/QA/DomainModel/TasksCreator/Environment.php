<?php

declare(strict_types=1);

namespace Freyr\QA\DomainModel\TasksCreator;

readonly class Environment
{

    public function __construct(
        public string $url
    )
    {

    }
}
