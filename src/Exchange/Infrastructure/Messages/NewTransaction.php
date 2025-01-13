<?php
declare(strict_types=1);

namespace Freyr\Exchange\Infrastructure\Messages;

use App\Domain\Message;

readonly class NewTransaction extends Message
{
    public function __construct(
        public string $sellId,
        public string $buyId,
        public string $seller,
        public string $buyer,
        public string $stock,
        public int    $numberOfShares,
        public int    $marketPrice,
    )
    {

    }
}
