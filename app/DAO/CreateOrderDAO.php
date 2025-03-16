<?php

namespace App\DAO;

use App\Repositories\OrderRepositoryInterface;
use Carbon\Carbon;
use Ramsey\Uuid\UuidInterface;

class CreateOrderDAO
{
    /**
     * @param array<Carbon> $timetables
     */
    public function __construct(
        public float $price,
        public string $phone,
        public array $timetables,
        public UuidInterface $productId,
    ) {}
}
