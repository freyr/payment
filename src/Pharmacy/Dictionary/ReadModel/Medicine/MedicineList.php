<?php

declare(strict_types=1);

namespace Freyr\Pharmacy\Dictionary\ReadModel\Medicine;

readonly class MedicineList
{

    /**
     * @var Medicine[]
     */
    public array $list;

    public function __construct(Medicine ...$medicineList)
    {
        $this->list = $medicineList;
    }
}