<?php

namespace App\Repositories;

use App\DAO\CreateOrderDAO;

Interface OrderRepositoryInterface
{
    public static function createOrder(CreateOrderDAO $arg): void;
}
