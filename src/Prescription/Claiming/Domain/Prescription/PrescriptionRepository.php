<?php

declare(strict_types=1);

namespace Freyr\Prescription\Claiming\Domain\Prescription;

use Freyr\Prescription\Issuing\Domain\Prescription\PrescriptionId;

interface PrescriptionRepository
{

    public function persist(Prescription $prescription): void;
    public function loadById(PrescriptionId $identity): Prescription;
}