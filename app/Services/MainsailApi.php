<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\Network;
use App\Services\Cache\MainsailCache;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

final class MainsailApi
{
    public static function fees(): array
    {
        $cache = new MainsailCache();

        $data = null;

        try {
            $data = Http::get(sprintf(
                '%s/node/fees',
                Network::api(),
            ))->json();
        } catch (\Throwable) {
            //
        }

        if ($data === null) {
            return $cache->getFees();
        }

        if (! Arr::has($data, 'data.evmCall')) {
            return $cache->getFees();
        }

        $fees = (new Collection(Arr::get($data, 'data.evmCall', [])))
            ->toArray();

        $cache->setFees($fees);

        return $fees;
    }

    public static function timeToForge(): int
    {
        return 1 * Network::blockTime();
    }
}
