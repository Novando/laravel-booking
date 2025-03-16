<?php

namespace App\DTO;

use Carbon\Carbon;
use Ramsey\Uuid\UuidInterface;

class CheckoutDTO
{
    /**
     * @param array<string> $sessions
     */
    public function __construct(
        public string $phone,
        public UuidInterface $productId,
        public Carbon $date,
        public array $sessions,
    ) {}
}
