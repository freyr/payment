<?php

declare(strict_types=1);

namespace Freyr\Prescription\Claiming\Domain\Prescription;

use Freyr\EventSourcing\AggregateChanged;
use Freyr\EventSourcing\AggregateRoot;
use Freyr\Prescription\Claiming\Domain\Fulfillment\FulfilPrescription;
use Freyr\Prescription\Claiming\Domain\Identity;
use Freyr\Prescription\Claiming\Domain\MedicineId;
use Freyr\Prescription\Claiming\Domain\PrescriptionItem;
use Freyr\Prescription\Claiming\Domain\PrescriptionList;
use Freyr\Prescription\Claiming\Domain\Pesel;
use Freyr\Prescription\Issuing\Domain\Prescription\PrescriptionId;

class Prescription extends AggregateRoot
{
    private Identity $identity;
    private PrescriptionList $prescriptions;

    public static function create(
        PrescriptionId $id,
        Identity $identity,
        PrescriptionList $list
    ): self {
        $prescription = new self($id);
        $prescription->recordThat(
            PrescriptionWasCreated::occur(
                [
                    'identity' => $identity,
                    'prescriptions' => $list
                ]
            )
        );

        return $prescription;
    }

    public function confirmIdentity(int $code, Pesel $pesel): bool
    {
        $identity = new Identity($code, $pesel);
        return $this->identity->sameAs($identity);
    }

    public function fill(FulfilPrescription $command): void
    {
        foreach ($command->getItems()->list as $item) {
            $this->prescriptions->fill($item);
        }
        if ($this->isFullyFilled()) {
            $this->recordThat(PrescriptionWasFulfilledCompletely::occur(Id::new()));
        } else {
            $this->recordThat(PrescriptionWasFulfilledPartially::occur(Id::new()));
        }
    }

    public function fulfill(
        PrescriptionItem $item,
    ): void {
        $this->prescriptions->fill($item);
        if (array_key_exists((string)$medicineId, $this->fulfilment) && $this->fulfilment[(string)$medicineId] > 0) {
            $prescriptionQuantity = $this->fulfilment[(string)$medicineId];
            $this->fulfilment[(string)$medicineId] = $prescriptionQuantity - $quantity;
        }
    }

    public function isFullyFilled(): bool
    {
        foreach ($this->fulfilment as $quantity) {
            if ($quantity > 0) {
                return false;
            }
        }

        return true;
    }

    protected function apply(AggregateChanged $event): void
    {
        // TODO: Implement apply() method.
    }

    static protected function eventDeserializer($eventName): callable
    {
        // TODO: Implement eventDeserializer() method.
    }
}