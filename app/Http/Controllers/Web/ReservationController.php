<?php

namespace App\Http\Controllers\Web;

use App\DTO\CalculatePriceDTO;
use App\DTO\GetProductDTO;
use App\Http\Controllers\Controller;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Ramsey\Uuid\Uuid;

class ReservationController extends Controller
{
    public function index(Request $req): Response
    {
        return Inertia::render('Reservation', [
            'products' => fn() => $this->getProducts(),
            'availableDates' => Inertia::lazy(fn() => $this->getAvailableDate($req)),
            'takenTimes' => Inertia::lazy(fn() => $this->getTakenTime($req)),
            'price' => Inertia::lazy(fn() => $this->calculatePrice($req)),
        ]);
    }

    /**
     * @return array<GetProductDTO>
     */
    public function getProducts(): array
    {
        return ReservationService::getProducts();
    }

    /**
     * @return array<string>
     */
    public function getAvailableDate(Request $req): array
    {
        $productId = $req->query('productId');
        return ReservationService::getAvailableDayByProductId(Uuid::fromString($productId));
    }

    /**
     * @return array<string>
     */
    public function getTakenTime(Request $req): array
    {
        $productId = $req->query('productId');
        $dateString = $req->query('date');
        $date = Carbon::parse($dateString)->startOfDay();
        return ReservationService::getTakenTime(Uuid::fromString($productId), $date);
    }

    public function calculatePrice(Request $req): float
    {
        $productId = $req->input('productId');
        $dateString = $req->input('date');
        $numOfSession = $req->input('numOfSession');
        $date = Carbon::parse($dateString)->startOfDay();
        $arg = new CalculatePriceDTO(productId: Uuid::fromString($productId), date: $date, numOfSession: $numOfSession);
        return ReservationService::calculatePrice($arg);
    }
}
