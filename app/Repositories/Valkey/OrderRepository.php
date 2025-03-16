<?php

namespace App\Repositories\Valkey;

use App\DAO\CreateOrderDAO;
use App\Repositories\OrderRepositoryInterface;
use Illuminate\Support\Facades\Redis;
use Ramsey\Uuid\Uuid;

class OrderRepository implements OrderRepositoryInterface
{

    public static function createOrder(CreateOrderDAO $arg): void
    {
        $id = Uuid::uuid4()->toString();
        $payload = [
            'id' => $id,
            'customers_phone' => $arg->phone,
            'price' => $arg->price,
            'timetables' => $arg->timetables,
        ];
        Redis::command('SET', [$id, json_encode($payload), 60*10]);
    }
}
