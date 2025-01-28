<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EvmFunctions;
use App\Facades\Network;
use App\Services\Cache\MainsailCache;
use ArkEcosystem\Crypto\Utils\AbiDecoder;
use ArkEcosystem\Crypto\Utils\UnitConverter;
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
            ->map(fn ($fee) => UnitConverter::parseUnits($fee, 'gwei'))
            ->toArray();

        // TODO: for QA purposes only - remove when ready - https://app.clickup.com/t/86dv7tt1a
        $fees['min'] = (string) BigNumber::new($fees['min'])->multipliedBy(0.5)->toNumber();
        $fees['max'] = (string) BigNumber::new($fees['max'])->multipliedBy(1.5)->toNumber();

        $cache->setFees($fees);

        return $fees;
    }

    public static function deployedTokenName(string $contractAddress): ?string
    {
        $response = Http::withHeader('Content-Type', 'application/json')
            ->post('https://dwallets-evm.ihost.org/evm/api', [
            'jsonrpc' => '2.0',
            'method'  => 'eth_call',
            'params'  => [[
                'from' => '0x12361f0Bd5f95C3Ea8BF34af48F5484b811B5CCe',
                'to'   => $contractAddress,
                'data' => EvmFunctions::NAME->value,
            ], 'latest'],
            'id' => 1,
        ]);

        $result = Arr::get($response->json(), 'result');
        if ($result === null) {
            return null;
        }

        $method = (new AbiDecoder())->decodeFunctionWithAbi('function name() view returns (string)', $result);

        return $method[0];
    }

    public static function timeToForge(): int
    {
        return 1 * Network::blockTime();
    }
}
