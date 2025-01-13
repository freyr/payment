<?php

declare(strict_types=1);

namespace Freyr\Prescription\Issuing\Application;

use Freyr\Prescription\Issuing\Domain\Medicine\Dosage;
use Freyr\Prescription\Issuing\Domain\Medicine\MedicineId;
use Freyr\Prescription\Issuing\Domain\Patient\Pesel;
use Freyr\Prescription\Issuing\Domain\Physician\PhysicianId;
use Freyr\Prescription\Issuing\Domain\Prescription\IssuePrescription;

class FakePrescriptionIssue implements IssuePrescription
{

    public function getPatientPesel(): Pesel
    {
        return new Pesel();
    }

    public function getDosages(): array
    {
        return [
            new Dosage(MedicineId::new(), '1', 1, 1)
        ];
    }

    public function getPhysicianId(): PhysicianId
    {
        return PhysicianId::new();
    }
}