<?php

declare(strict_types=1);

namespace Freyr\Prescription\Claiming\Domain\Fulfillment;

use Freyr\Prescription\Claiming\Domain\PrescriptionList;
use Freyr\Prescription\Issuing\Domain\Prescription\PrescriptionId;

interface FulfilPrescription
{

    public function getId(): PrescriptionId;

    public function getItems(): PrescriptionList;
}