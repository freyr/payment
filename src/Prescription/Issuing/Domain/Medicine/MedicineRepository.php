<?php

declare(strict_types=1);

namespace Freyr\Prescription\Issuing\Domain\Medicine;

interface MedicineRepository
{

    public function check(Dosage ...$dosage): bool;
}