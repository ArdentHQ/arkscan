<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\Network;
use App\Services\Cache\MainsailCache;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use Illuminate\Support\Arr;
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

        $fees = collect(Arr::get($data, 'data.evmCall', []))
            ->map(fn ($fee) => UnitConverter::parseUnits($fee, 'gwei'))
            ->toArray();

        // TODO: for QA purposes only - remove when ready - https://app.clickup.com/t/86dv7tt1a
        $fees['min'] = (string) BigNumber::new($fees['min'])->multipliedBy(0.5)->toNumber();
        $fees['max'] = (string) BigNumber::new($fees['max'])->multipliedBy(1.5)->toNumber();

        $cache->setFees($fees);

        return $fees;
    }

    public static function timeToForge(): int
    {
        return 1 * Network::blockTime();
    }
}
