<?php

declare(strict_types=1);

namespace Freyr\Prescription\Claiming\Domain;

final readonly class Pesel
{
    public function __construct(private string $number)
    {

    }
    public function sameAs(Pesel $pesel): bool
    {
        return (string) $this === (string) $pesel;
    }

    public function __toString(): string
    {
        return $this->number;
    }
}