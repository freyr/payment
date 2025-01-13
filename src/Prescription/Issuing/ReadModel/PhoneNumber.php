<?php

declare(strict_types=1);

namespace Freyr\Prescription\Issuing\ReadModel;

final readonly class PhoneNumber
{
    public function __construct(private string $phoneNumber)
    {

    }
}