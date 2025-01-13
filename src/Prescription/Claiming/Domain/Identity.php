<?php

declare(strict_types=1);

namespace Freyr\Prescription\Claiming\Domain;

final readonly class Identity
{

    public function __construct(
        public int $code,
        public Pesel $pesel
    )
    {

    }
    public function sameAs(Identity $identity): bool
    {
        return $this->code === $identity->code && $this->pesel->sameAs($identity->pesel);
    }

    public function __toString(): string
    {
        return $this->code . '-' . $this->pesel;
    }
}