<?php

declare(strict_types=1);

namespace Freyr\Prescription\Issuing\Domain\Prescription;

enum PrescriptionStatus: string
{
    case CANCELLED = 'cancelled';
    case ISSUED = 'issued';
}