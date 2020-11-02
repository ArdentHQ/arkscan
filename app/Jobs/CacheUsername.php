<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

final class CacheUsername implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Wallet $wallet;

    public function __construct(Wallet $wallet)
    {
        $this->wallet = $wallet;
    }

    public function handle()
    {
        $username = $this->wallet->attributes['delegate']['username'];

        Cache::put(sprintf('%s:username', $this->wallet->address), $username);

        if (! is_null($this->wallet->public_key)) {
            Cache::put(sprintf('%s:username', $this->wallet->public_key), $username);
        }
    }
}
