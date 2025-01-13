<?php

declare(strict_types=1);

namespace Freyr\Pharmacy\Dictionary\ReadModel\Medicine;

use Freyr\Prescription\Claiming\Domain\MedicineId;

class Medicine
{
    public function __construct(
        public MedicineId $id,
        public string $name,
    ) {

    }
}