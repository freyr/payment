<?php

declare(strict_types=1);

namespace Freyr\Payment\Domain\Transaction;

enum TransactionState: string
{
    case FINALIZED = 'FINALIZED';
    case CREATED = 'CREATED';
}