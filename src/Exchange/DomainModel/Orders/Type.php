<?php
declare(strict_types=1);

namespace Freyr\Exchange\DomainModel\Orders;

enum Type: string
{
    case LIMIT = 'limit';
    case MARKET = 'market';
    case STOP_LOSS = 'stop_loss';
}
