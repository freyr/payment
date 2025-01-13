<?php

declare(strict_types=1);

namespace Freyr\Pharmacy\Dictionary\Domain;

interface FetchMedicineList
{
    public function getSearchPhrase(): string;
}