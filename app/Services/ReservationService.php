<?php

namespace App\Services;


use App\Constants;
use App\DAO\ReservationDAO;
use App\DTO\CalculatePriceDTO;
use App\DTO\GetProductDTO;
use App\Repositories\MySQL\ReservationRepository;
use Carbon\Carbon;
use Carbon\Traits\Date;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ReservationService
{
    /**
     * @return array<GetProductDTO>
     */
    public static function getProducts(): array
    {
        $res = [];
        foreach (Constants::PRODUCT as $k => $v) {
            $res[] = new GetProductDTO(
                value: str_replace('-', '', Uuid::fromString($v)->toString()),
                label: $k,
            );
        };
        return $res;
    }

    /**
     * @return array<string>
     */
    public static function getAvailableDayByProductId(UuidInterface $productId): array
    {
        $data = ReservationRepository::getTimetablesCountByProductId($productId);
        $currDate = Carbon::today();
        $endDate = Carbon::today()->addMonth();
        $excludeDate = [];
        $res = [];
        foreach ($data as $timetable) {
            if ($timetable->count >= 24) { $excludeDate[] = $timetable->date->format('Y-m-d'); }
        }
        while ($currDate->unix() <= $endDate->unix()) {
            $currString = $currDate->format('Y-m-d');
            if (!in_array($currString, $excludeDate)) {$res[] = $currString;}
            $currDate = $currDate->addDay();
        }
        return $res;
    }
    /**
     * @return array<string>
     */
    public static function getTakenTime(UuidInterface $productId, Carbon $time): array
    {
        $data = ReservationRepository::getFilledTimetables($productId, $time);
        $res = [];
        foreach ($data as $timetable) {
            $res[] = $timetable->add('hours', -7)->format('h:\0\0');
        }
        return $res;
    }

    public static function calculatePrice(CalculatePriceDTO $arg): float
    {
        $price = 0;
        if (in_array($arg->date->dayOfWeek, [6, 7])) { $price = Constants::WEEKEND_SURCHARGE; }
        $productId = str_replace('-', '', $arg->productId->toString());
        $productName = array_search($productId, Constants::PRODUCT);
        $productPrice = Constants::PRICING[$productName] * $arg->numOfSession;
        return $price + $productPrice;
    }
}
