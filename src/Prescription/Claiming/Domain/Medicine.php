<?php

declare(strict_types=1);

namespace Freyr\Prescription\Claiming\Domain;

class Medicine
{
    public function __construct(
        public MedicineId $id,
        public string $name,
    ) {

    }

}