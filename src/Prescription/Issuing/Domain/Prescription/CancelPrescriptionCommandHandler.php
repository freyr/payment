<?php

declare(strict_types=1);

namespace Freyr\Prescription\Issuing\Domain\Prescription;

final readonly class CancelPrescriptionCommandHandler
{

    public function __construct(
        private PrescriptionRepository $prescriptionRepository
    )
    {

    }

    public function __invoke(CancelPrescription $command): void
    {
        $prescription = $this->prescriptionRepository->loadById($command->prescriptionId());
        $prescription->cancel($command);
        $this->prescriptionRepository->persist($prescription);
    }
}