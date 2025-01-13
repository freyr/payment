<?php

declare(strict_types=1);

namespace Freyr\Prescription\Claiming\Domain;

readonly class PrescriptionList
{

    /**
     * @var PrescriptionItem[]
     */
    public array $list;

    public function __construct(PrescriptionItem ...$prescriptionItem)
    {
        $sortedItems = [];
        foreach ($prescriptionItem as $item) {
            $sortedItems[(string) $item->medicineId] = $item;
        }
        $this->list = $sortedItems;
    }

    public function fill(PrescriptionItem $item): void
    {
        $prescriptionItem = $this->list[(string) $item->medicineId];
        $prescriptionItem->fill($item);
    }
}