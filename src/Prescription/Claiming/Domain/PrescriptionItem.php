<?php

declare(strict_types=1);

namespace Freyr\Prescription\Claiming\Domain;

class PrescriptionItem
{
    public MedicineId $medicineId {
        get => $this->medicine->id;
    }

    public function __construct(
        readonly private Medicine $medicine,
        public int $quantity,
    )
    {

    }

    public function fill(PrescriptionItem $item): void
    {
        $this->quantity -= $item->quantity;
    }
}