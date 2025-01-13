<?php

declare(strict_types=1);

namespace Freyr\Prescription\Issuing\Domain\Medicine;

class Dosage
{

    public function __construct(
        MedicineId $medicineId,
        $variant,
        $size,
        $quantity
    ) {
    }
}