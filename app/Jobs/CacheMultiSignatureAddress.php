<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use App\Services\MultiSignature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

final class CacheMultiSignatureAddress implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Wallet $wallet;

    public function __construct(Wallet $wallet)
    {
        $this->wallet = $wallet;
    }

    public function handle(): void
    {
        $min        = Arr::get($this->wallet->attributes, 'multiSignature.min', 0);
        $publicKeys = Arr::get($this->wallet->attributes, 'multiSignature.publicKeys', []);

        (new WalletCache())->setMultiSignatureAddress($min, $publicKeys, fn () => MultiSignature::address($min, $publicKeys));
    }
}
