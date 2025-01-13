<?php

declare(strict_types=1);

namespace Freyr\Prescription\Issuing\Domain\Prescription;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\EventSourcing\AggregateRoot;
use Freyr\Prescription\Issuing\Domain\Medicine\Dosage;
use Freyr\Prescription\Issuing\Domain\Medicine\MedicineRepository;
use Freyr\Prescription\Issuing\Domain\Patient\Patient;
use Freyr\Prescription\Issuing\Domain\Physician\Physician;
use RuntimeException;

class Prescription extends AggregateRoot
{
    private int $code;
    private Patient $patient;
    private Dosage $dosage;
    private Physician $physician;
    private PrescriptionStatus $status;

    public static function create(
        MedicineRepository $medicineRepository,
        PrescriptionId $id,
        Patient $patient,
        Physician $physician,
        PrescriptionStatus $status = PrescriptionStatus::ISSUED,
        Dosage ...$dosages,
    ): self {
        $prescription = new self();
        if (empty($dosages)) {
            throw new RuntimeException('Dosages should not be empty.');
        }

        foreach ($dosages as $dosage) {
            if (!$medicineRepository->check($dosage)) {
                throw new RuntimeException('Incorrect medicine');
            }
        }

        $prescription->recordThat(
            PrescriptionWasIssued::occur(
                $id,
                [
                    'patient' => $patient,
                    'physician' => $physician,
                    'status' => $status,
                    'dosages' => $dosages,
                    'code' => rand(1, 9999),
                ]
            )
        );

        return $prescription;
    }

    public function cancel(CancelPrescription $command): void
    {
        if ($this->physician !== $command->getPhysician()) {
            throw new CannotCancelPrescriptionException();
        }
        $this->status = PrescriptionStatus::CANCELLED;
        $this->recordThat(
        PrescriptionCanceled::occur(
            $this->id,
            [
                'patientId' => (string) $this->patient->getId(),
                'status' => $this->status->value
            ]
        )
    );
    }

    protected function apply(AggregateChanged $event): void
    {
    }

    static protected function eventDeserializer($eventName): callable
    {
        // TODO: Implement eventDeserializer() method.
    }
}