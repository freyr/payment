<?php

declare(strict_types=1);

namespace Freyr\Payment\Domain\Transaction;

enum TransactionResult: string
{
    case FAILED = 'FAILED';
    case PENDING = 'PENDING';
}