<?php

namespace App\Console\Commands;

use App\Http\Controllers\Redis\ExpirationController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class ListenForKeyExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:listen-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen for expired keys in Redis';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Redis::command('CONFIG', ['SET', 'notify-keyspace-events', 'Ex']);
        $this->info('Listening for expired keys...');

        Redis::subscribe(['__keyevent@0__:expired'], function ($message) {
            $this->info("Key expired: $message");

            // Call your callback function here
            ExpirationController::listen($message);
        });
    }
}
