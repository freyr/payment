<?php

declare(strict_types=1);

namespace Freyr\Payment\Domain\Transaction;

enum TransactionResult: string
{
    case PENDING = 'PENDING';
    case SUCCESS = 'SUCCESS';
    case FAILED = 'FAILED';
}
