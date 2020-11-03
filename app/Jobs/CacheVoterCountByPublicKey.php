<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class CacheVoterCountByPublicKey implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $publicKey;

    public function __construct(string $publicKey)
    {
        $this->publicKey = $publicKey;
    }

    public function handle(): void
    {
        (new WalletCache())->setVoterCount($this->publicKey, Wallet::where('attributes->vote', $this->publicKey)->count());
    }
}
