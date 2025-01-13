<?php

declare(strict_types=1);

namespace Freyr\Prescription\Issuing\Domain\Prescription;

interface PrescriptionRepository
{

    public function persist(Prescription $prescription): void;

    public function loadById(PrescriptionId $prescriptionId): Prescription;
}