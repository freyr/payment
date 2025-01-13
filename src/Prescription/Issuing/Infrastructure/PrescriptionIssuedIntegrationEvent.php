<?php

declare(strict_types=1);

namespace Freyr\Prescription\Issuing\Infrastructure;

final readonly class PrescriptionIssuedIntegrationEvent
{
    public function __construct(
        public string $id,
        public string $patientId,
        public int $code
    )
    {
    }
}