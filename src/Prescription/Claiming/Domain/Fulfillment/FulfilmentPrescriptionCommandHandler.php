<?php

declare(strict_types=1);

namespace Freyr\Prescription\Claiming\Domain\Fulfillment;

use Freyr\Pharmacy\Storage\Application\PrescriptionClaiming\IsInStock;
use Freyr\Prescription\Claiming\Domain\Prescription\PrescriptionRepository;

final readonly class FulfilmentPrescriptionCommandHandler
{

    public function __construct(
        private PrescriptionRepository $repository,
    )
    {
    }

    public function __invoke(FulfilPrescription $command): void
    {
        $isInStock = new IsInStock();
        $prescription = $this->repository->loadById($command->getId());
        $prescription->fill($command);
        $this->repository->persist($prescription);

    }
}