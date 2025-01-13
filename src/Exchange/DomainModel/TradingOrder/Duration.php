<?php
declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\TradingOrder;

enum Duration: string
{
    case DAY = 'day';
    case GTC = 'good_till_cancelled';
}
