<?php

declare(strict_types=1);

namespace Freyr\Prescription\Issuing\Domain\Patient;

interface PatientRepository
{
    public function findByPesel(Pesel $pesel): Patient;
}