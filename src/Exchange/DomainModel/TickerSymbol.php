<?php

declare(strict_types=1);

namespace Freyr\Exchange\DomainModel;

enum TickerSymbol: string
{
    case APPL = 'AAPL';
    case BUD = 'BUD';
    case WOOF = 'WOOF';
    case GOOG = 'GOOG';
    case HOG = 'HOG';
    case KO = 'KO';
}
