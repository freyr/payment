<?php

declare(strict_types=1);

namespace Freyr\Prescription\Issuing\Domain\Prescription;

use Freyr\Prescription\Issuing\Domain\Medicine\MedicineRepository;
use Freyr\Prescription\Issuing\Domain\Patient\PatientRepository;
use Freyr\Prescription\Issuing\Domain\Physician\PhysicianRepository;

final readonly class IssuePrescriptionCommandHandler
{

    public function __construct(
        private PatientRepository $patientRepository,
        private MedicineRepository $medicineRepository,
        private PrescriptionRepository $prescriptionRepository,
        private PhysicianRepository $physicianRepository
    )
    {

    }
    public function __invoke(IssuePrescription $command): void
    {
        $patient = $this->patientRepository->findByPesel($command->getPatientPesel());
        $physician = $this->physicianRepository->getById($command->getPhysicianId());

        $prescription = Prescription::issue(
            $this->medicineRepository,
            $patient,
            $physician,
            ...$command->getDosages()
        );

        $this->prescriptionRepository->persist($prescription);
    }
}