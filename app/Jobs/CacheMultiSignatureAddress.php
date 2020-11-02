<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Wallet;
use App\Services\MultiSignature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

final class CacheMultiSignatureAddress implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Wallet $wallet;

    public function __construct(Wallet $wallet)
    {
        $this->wallet = $wallet;
    }

    public function handle()
    {
        $min        = Arr::get($this->wallet->attributes, 'multiSignature.min', 0);
        $publicKeys = Arr::get($this->wallet->attributes, 'multiSignature.publicKeys', []);

        Cache::put(
            sprintf('multiSignature:%s:%s', $min, serialize($publicKeys)),
            MultiSignature::address($min, $publicKeys)
        );
    }
}
