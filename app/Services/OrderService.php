<?php

namespace App\Services;

use App\DAO\CreateOrderDAO;
use App\DTO\CalculatePriceDTO;
use App\DTO\CheckoutDTO;
use App\Repositories\MySQL\ReservationRepository;
use App\Repositories\Valkey\OrderRepository;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class OrderService
{
    public static function createOrder(CheckoutDTO $arg): void
    {
        $calculateParam = new CalculatePriceDTO(
            productId: $arg->productId,
            date: $arg ->date,
            numOfSession: count($arg->sessions),
        );
        $price = ReservationService::calculatePrice($calculateParam);
        $dateString = $arg->date->format('Y-m-d');
        $timetables = [];
        foreach ($arg->sessions as $time) {
            $timetables[] = Carbon::parse("{$dateString}T{$time}:00+07:00")->utc();
        }
        $orderParam = new CreateOrderDAO(
            price: $price,
            phone: $arg->phone,
            timetables: $timetables,
            productId: $arg->productId,
        );
        ReservationRepository::fillTimetables($arg->productId, $timetables);
        OrderRepository::createOrder($orderParam);
    }
}
