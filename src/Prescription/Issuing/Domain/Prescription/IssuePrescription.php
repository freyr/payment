<?php

declare(strict_types=1);

namespace Freyr\Prescription\Issuing\Domain\Prescription;

use Freyr\Prescription\Issuing\Domain\Medicine\Dosage;
use Freyr\Prescription\Issuing\Domain\Patient\Pesel;
use Freyr\Prescription\Issuing\Domain\Physician\PhysicianId;

interface IssuePrescription
{

    public function getPatientPesel(): Pesel;

    /**
     * @return array<int, Dosage>
     */
    public function getDosages(): array;

    public function getPhysicianId(): PhysicianId;
}