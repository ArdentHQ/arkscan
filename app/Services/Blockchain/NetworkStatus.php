<?php

declare(strict_types=1);

namespace App\Services\Blockchain;

use App\Facades\Network;
use App\Models\Block;
use App\Models\Wallet;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class NetworkStatus
{
    public static function height(): int
    {
        return Cache::remember('status.height:'.Network::name(), 8000, function () {
            return (int) Block::latestByHeight()->first()['height'];
        });
    }

    // @TODO: use the wallets table once it is available.
    public static function supply(): int
    {
        return Cache::remember('status.supply:'.Network::name(), 8000, function () {
            $supply = Http::baseUrl(Network::host())->get('blockchain')['data']['supply'];

            return intval($supply / 1e8);
        });

        // return Cache::remember('status.supply:'.Network::name(), 8000, fn () => Wallet::sum('balance'));
    }
}
