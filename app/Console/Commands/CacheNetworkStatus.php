<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use Illuminate\Console\Command;

final class CacheNetworkStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-network-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the height and supply of the network.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(NetworkCache $cache)
    {
        $cache->setHeight($this->getHeight());

        $cache->setSupply($this->getSupply());
    }

    private function getHeight(): int
    {
        $block = Block::withScope(OrderByHeightScope::class)->first();

        if (is_null($block)) {
            return 0;
        }

        return $block->height->toNumber();
    }

    private function getSupply(): string
    {
        return (string) Wallet::where('balance', '>', 0)->sum('balance');
    }
}
