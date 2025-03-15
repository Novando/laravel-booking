<?php

namespace App\DTO;

use Carbon\Carbon;
use Ramsey\Uuid\UuidInterface;

class CalculatePriceDTO
{
    public function __construct(
        public UuidInterface $productId,
        public Carbon $date,
        public int $numOfSession,
    ) {}
}
