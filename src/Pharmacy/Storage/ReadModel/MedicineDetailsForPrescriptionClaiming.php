<?php

declare(strict_types=1);

namespace Freyr\Pharmacy\Storage\ReadModel;

final readonly class MedicineDetailsForPrescriptionClaiming
{
    public string $id;
    public string $name;
    public \DateTimeImmutable $boughtAt;
}