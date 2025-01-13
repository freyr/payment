<?php

declare(strict_types=1);

namespace Freyr\Prescription\Issuing\ReadModel;

use Freyr\Prescription\Issuing\Domain\Prescription\PrescriptionId;

final readonly class PrescriptionForPatientReadModel
{
    public function __construct(
        public PrescriptionId $id,
        public int $code,
        public PhoneNumber $patientPhoneNumber,
    )
    {

    }
}