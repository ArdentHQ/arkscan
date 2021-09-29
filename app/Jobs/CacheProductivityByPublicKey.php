<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\ForgingStats;
use App\Services\Cache\WalletCache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mattiasgeniar\Percentage\Percentage;

final class CacheProductivityByPublicKey implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public string $publicKey)
    {
    }

    public function handle(): void
    {
        $missed = ForgingStats::where('forged', false)->where('public_key', $this->publicKey)->count();
        $forged = ForgingStats::where('forged', true)->where('public_key', $this->publicKey)->count();
        $total  = $forged + $missed;

        $walletCache = new WalletCache();

        $walletCache->setMissedBlocks(
            $this->publicKey,
            $missed
        );

        $walletCache->setProductivity(
            $this->publicKey,
            $total > 0 ? Percentage::calculate($forged, $total) : -1
        );
    }
}
