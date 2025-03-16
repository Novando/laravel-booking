<?php

namespace App\Providers;

use App\Http\Controllers\Redis\ExpirationController;
use App\Repositories\OrderRepositoryInterface;
use App\Repositories\Valkey\OrderRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\ReservationRepositoryInterface;
use App\Repositories\MySQL\ReservationRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ReservationRepositoryInterface::class, ReservationRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
