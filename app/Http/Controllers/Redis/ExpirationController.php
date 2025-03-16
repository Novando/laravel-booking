<?php

namespace App\Http\Controllers\Redis;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ExpirationController extends Controller
{
    public static function listen(string $message): void
    {
        Log::info('kedengeran broh!!!');
        json_decode($message);
    }
}
