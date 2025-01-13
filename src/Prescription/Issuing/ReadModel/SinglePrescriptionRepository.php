<?php

declare(strict_types=1);

namespace Freyr\Prescription\Issuing\ReadModel;

interface SinglePrescriptionRepository
{

    public function update(string $patientId, int $code);
}