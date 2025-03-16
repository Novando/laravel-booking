<?php

use App\Http\Controllers\Web\ReservationController;
use App\Http\Controllers\Web\OrderController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    Log::info('Welcome page visited');
    return view('welcome');
});

Route::get('/app', [ReservationController::class, 'index']);
Route::post('/checkout', [OrderController::class, 'checkout']);
