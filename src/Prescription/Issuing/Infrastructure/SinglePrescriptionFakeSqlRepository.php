<?php

declare(strict_types=1);

namespace Freyr\Prescription\Issuing\Infrastructure;

use Freyr\Prescription\Issuing\ReadModel\SinglePrescriptionRepository;

class SinglePrescriptionFakeSqlRepository implements SinglePrescriptionRepository
{

    public function update(string $patientId, int $code): void
    {
        $patientPhoneNumber = 'select phone_nbumber from patients_data where patient_id = :patientId';

        $sql = 'insert into single_prescriptions (id, code, phone_number) 
                values (:id, :code, :phone_number)';

    }
}