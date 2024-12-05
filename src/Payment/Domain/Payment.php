<?php

declare(strict_types=1);

namespace Freyr\Payment\Domain;

class Payment
{

    public static function create(): self
    {

    }

    public function requestExecution(): void
    {
        // create Transaction
    }

    public function resolveExecutionRequest(): void
    {
        // finalize Transaction
    }
}
