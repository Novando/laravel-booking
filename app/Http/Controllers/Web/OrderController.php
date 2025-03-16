<?php

namespace App\Http\Controllers\Web;

use App\DTO\CheckoutDTO;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Ramsey\Uuid\Uuid;

class OrderController
{
    public function checkout (Request $req): View
    {
        $productId = $req->input('productId');
        $dateString = $req->input('date');
        $arg = new CheckoutDTO(
            phone: $req->input('phone'),
            productId: Uuid::fromString($productId),
            date: Carbon::parse($dateString)->startOfDay(),
            sessions: $req->input('sessions'),
        );
        OrderService::createOrder($arg);
        return view('welcome');
    }
}
