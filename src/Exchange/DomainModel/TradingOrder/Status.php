<?php

declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\TradingOrder;

enum Status: string
{
    case OPEN = 'open';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
    case FULFILLED = 'fulfilled';
    case INACTIVE = 'inactive';
}
