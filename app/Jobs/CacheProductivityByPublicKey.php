<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Facades\Network;
use App\Models\Block;
use App\Services\Cache\WalletCache;
use App\Services\Timestamp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mattiasgeniar\Percentage\Percentage;

final class CacheProductivityByPublicKey implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $publicKey)
    {
    }

    public function handle(): void
    {
        $blocksTotal            = (86400 * 30) / Network::blockTime();
        $blocksDelegateExpected = (int) ceil($blocksTotal / Network::delegateCount());
        $blocksDelegateActual   = Block::query()
            ->where('timestamp', '>=', Timestamp::now()->subDays(30)->unix())
            ->where('generator_public_key', $this->publicKey)
            ->count();

        (new WalletCache())->setProductivity(
            $this->publicKey,
            Percentage::calculate($blocksDelegateActual, $blocksDelegateExpected)
        );
    }
}
