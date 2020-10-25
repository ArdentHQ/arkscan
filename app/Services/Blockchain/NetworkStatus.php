<?php

declare(strict_types=1);

namespace App\Services\Blockchain;

use App\Facades\Network;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\BigNumber;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class NetworkStatus
{
    public static function height(): int
    {
        return (int) Cache::remember('status.height:'.Network::name(), 8000, function (): int {
            $block = Block::latestByHeight()->first();

            if (is_null($block)) {
                return 0;
            }

            return $block->height->toNumber();
        });
    }

    // @TODO: use the wallets table once it is available.
    public static function supply(): int
    {
        return (int) Cache::remember('status.supply:'.Network::name(), 8000, function (): float {
            $supply = Http::baseUrl(Network::host())->get('blockchain')['data']['supply'];

            return BigNumber::new($supply)->toFloat();
        });

        // return Cache::remember('status.supply:'.Network::name(), 8000, fn () => Wallet::sum('balance'));
    }
}
