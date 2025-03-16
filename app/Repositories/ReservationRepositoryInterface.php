<?php

namespace App\Repositories;

use App\DAO\ReservationDAO;
use Carbon\Carbon;
use Ramsey\Uuid\UuidInterface;

interface ReservationRepositoryInterface
{
    /**
     * @return array<ReservationDAO>
     */
    public static function getTimetablesCountByProductId(UuidInterface $productId): array;

    /**
     * @return array<Carbon>
     */
    public static function getFilledTimetables(UuidInterface $productId, Carbon $time): array;

    /**
     * @param array<Carbon> $time
     */
    public static function fillTimetables(UuidInterface $productId, array $time): void;
}
