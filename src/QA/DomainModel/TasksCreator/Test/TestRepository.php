<?php

declare(strict_types=1);

namespace Freyr\QA\DomainModel\TasksCreator\Test;

interface TestRepository
{

    public function save(Test ...$tests): void;
}
