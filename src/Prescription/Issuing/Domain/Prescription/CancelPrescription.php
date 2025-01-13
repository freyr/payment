<?php

declare(strict_types=1);

namespace Freyr\Prescription\Issuing\Domain\Prescription;

use Freyr\Prescription\Issuing\Domain\Physician\Physician;

interface CancelPrescription
{

    public function prescriptionId(): PrescriptionId;

    public function getPhysician(): Physician;
}