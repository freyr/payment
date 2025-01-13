<?php

declare(strict_types=1);

namespace Freyr\Prescription\Issuing\Domain\Prescription;

use Freyr\Prescription\EventSourcing\AggregateChanged;

readonly class PrescriptionCanceled extends AggregateChanged
{

}