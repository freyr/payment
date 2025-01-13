<?php

declare(strict_types=1);

namespace Freyr\Prescription\Issuing\Domain\Physician;

interface PhysicianRepository
{

    public function getById(PhysicianId $getPhysicianId);
}