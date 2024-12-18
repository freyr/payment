<?php
declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\Orders;

enum Kind: string
{
    case BUY = 'buy';
    case SELL = 'sell';
}
