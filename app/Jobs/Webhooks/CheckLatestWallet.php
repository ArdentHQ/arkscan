<?php

declare(strict_types=1);

namespace App\Jobs\Webhooks;

use App\Events\Statistics\UniqueAddresses;
use App\Services\Addresses\Aggregates\LatestWalletAggregate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

final class CheckLatestWallet implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function handle(): void
    {
        $latestWallet = (new LatestWalletAggregate())->aggregate();
        if ($latestWallet === null) {
            return;
        }

        UniqueAddresses::dispatch();
    }
}
