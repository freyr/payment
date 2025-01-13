<?php

declare(strict_types=1);

namespace Freyr\Prescription\Issuing\Domain\Physician;

use Freyr\Prescription\Issuing\Domain\Medicine\Medicine;
use Freyr\Prescription\Issuing\Domain\Patient\Patient;

interface PhysicianPrescriptionStatus
{

    public function canIssue(Physician $issuer, Patient $patient, Medicine $medication);
}