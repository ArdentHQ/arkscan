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

final class CacheProductivityByAddress implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public string $address)
    {
    }

    public function handle(): void
    {
        $missed = ForgingStats::where('forged', false)->where('address', $this->address)->count();
        $forged = ForgingStats::where('forged', true)->where('address', $this->address)->count();
        $total  = $forged + $missed;

        $walletCache = new WalletCache();

        $walletCache->setMissedBlocks(
            $this->address,
            $missed
        );

        $walletCache->setProductivity(
            $this->address,
            $total > 0 ? Percentage::calculate($forged, $total) : 0
        );
    }
}
