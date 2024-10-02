<?php

declare(strict_types=1);

namespace Tests;

use App\Console\Commands\CacheDelegatePerformance;
use App\Facades\Network;
use App\Facades\Rounds;
use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\WalletCache;
use App\Services\Monitor\DelegateTracker;
use App\Services\Monitor\Monitor;
use App\Services\Timestamp;
use ArkEcosystem\Crypto\Identities\PublicKey;
use Faker\Generator;
use FurqanSiddiqui\BIP39\BIP39;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

function faker(): Generator
{
    return app(Generator::class);
}

function fakeKnownWallets(): void
{
    Http::fake([
        'githubusercontent.com/*' => [
            [
                'type'    => 'team',
                'name'    => 'ACF Hot Wallet',
                'address' => 'AagJoLEnpXYkxYdYkmdDSNMLjjBkLJ6T67',
            ], [
                'type'    => 'team',
                'name'    => 'ACF Hot Wallet (old)',
                'address' => 'AWkBFnqvCF4jhqPSdE2HBPJiwaf67tgfGR',
            ], [
                'type'    => 'exchange',
                'name'    => 'Altilly',
                'address' => 'ANvR7ny44GrLy4NTfuVqjGYr4EAwK7vnkW',
            ], [
                'type'    => 'team',
                'name'    => 'ARK Bounty',
                'address' => 'AXxNbmaKspf9UqgKhfTRDdn89NidP2gXWh',
            ], [
                'type'    => 'team',
                'name'    => 'ARK Bounty Hot Wallet',
                'address' => 'AYCTHSZionfGoQsRnv5gECEuFWcZXS38gs',
            ], [
                'type'    => 'team',
                'name'    => 'ARK GitHub Bounty',
                'address' => 'AZmQJ2P9xg5j6VPZWjcTzWDD4w7Qww2KGX',
            ], [
                'type'    => 'team',
                'name'    => 'ARK Hot Wallet',
                'address' => 'ANkHGk5uZqNrKFNY5jtd4A88zzFR3LnJbe',
            ], [
                'type'    => 'team',
                'name'    => 'ARK Shield',
                'address' => 'AHJJ29sCdR5UNZjdz3BYeDpvvkZCGBjde9',
            ], [
                'type'    => 'team',
                'name'    => 'ARK Shield (old)',
                'address' => 'AdTyTzaXPtj1J1DzTgVksa9NYdUuXCRbm1',
            ], [
                'type'    => 'team',
                'name'    => 'ARK Team',
                'address' => 'AXzxJ8Ts3dQ2bvBR1tPE7GUee9iSEJb8HX',
            ], [
                'type'    => 'team',
                'name'    => 'ARK Team (old)',
                'address' => 'AUDud8tvyVZa67p3QY7XPRUTjRGnWQQ9Xv',
            ], [
                'type'    => 'exchange',
                'name'    => 'Binance',
                'address' => 'AFrPtEmzu6wdVpa2CnRDEKGQQMWgq8nE9V',
            ], [
                'type'    => 'exchange',
                'name'    => 'Binance Cold Wallet',
                'address' => 'AQkyi31gUbLuFp7ArgH9hUCewg22TkxWpk',
            ], [
                'type'    => 'exchange',
                'name'    => 'Binance Cold Wallet II',
                'address' => 'AdS7WvzqusoP759qRo6HDmUz2L34u4fMHz',
            ], [
                'type'    => 'exchange',
                'name'    => 'Binance Cold Wallet III',
                'address' => 'Aakg29vVhQhJ5nrsAHysTUqkTBVfmgBSXU',
            ], [
                'type'    => 'exchange',
                'name'    => 'Binance Cold Wallet IV',
                'address' => 'AazoqKvZQ7HKZMQ151qaWFk6nDY1E9faYu',
            ], [
                'type'    => 'exchange',
                'name'    => 'Bittrex',
                'address' => 'AUexKjGtgsSpVzPLs6jNMM6vJ6znEVTQWK',
            ], [
                'type'    => 'exchange',
                'name'    => 'Changelly',
                'address' => 'AdA5THjiVFAWhcMo5QyTKF1Y6d39bnPR2F',
            ], [
                'type'    => 'exchange',
                'name'    => 'COSS',
                'address' => 'AcPwcdDbrprJf8FNCE3dKZaTvPJT8y4Cqi',
            ], [
                'type'    => 'exchange',
                'name'    => 'Cryptopia',
                'address' => 'AJbmGnDAx9y91MQCDApyaqZhn6fBvYX9iJ',
            ], [
                'type'    => 'exchange',
                'name'    => 'Genesis Wallet',
                'address' => 'AewxfHQobSc49a4radHp74JZCGP8LRe4xA',
            ], [
                'type'    => 'exchange',
                'name'    => 'Livecoin',
                'address' => 'AcVHEfEmFJkgoyuNczpgyxEA3MZ747DRAu',
            ], [
                'type'    => 'exchange',
                'name'    => 'OKEx',
                'address' => 'AZcK6t1P9Z2ndiYvdVaS7srzYbTn5DHmck',
            ], [
                'type'    => 'exchange',
                'name'    => 'Upbit',
                'address' => 'ANQftoXeWoa9ud9q9dd2ZrUpuKinpdejAJ',
            ], [
                'type'    => 'exchange',
                'name'    => 'Upbit Cold Wallet',
                'address' => 'AdzbhuDTyhnfAqepZzVcVsgd1Ym6FgETuW',
            ], [
                'type'    => 'exchange',
                'name'    => 'Upbit Hot Wallet',
                'address' => 'AReY3W6nTv3utiG2em5nefKEsGQeqEVPN4',
            ],
        ],
    ]);
}

function fakeCryptoCompare(bool $setToZero = false, string $currency = 'USD'): void
{
    $histohour = 'histohour'.($setToZero ? '-zero' : '');

    Http::fake([
        'cryptocompare.com/data/pricemultifull*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/pricemultifull.json')), true), 200),
        'cryptocompare.com/data/price*'          => Http::response([Str::upper($currency) => 0.2907, 'BTC' => 0.00002907], 200),
        'cryptocompare.com/data/histoday*'       => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/historical.json')), true), 200),
        'cryptocompare.com/data/histohour*'      => Http::response(json_decode(file_get_contents(base_path("tests/fixtures/cryptocompare/{$histohour}.json")), true), 200),
    ]);
}

function bip39(): string
{
    return PublicKey::fromPassphrase((implode(' ', BIP39::Generate()->words)))->getHex();
}

function getDelegateWallets(): SupportCollection
{
    return Wallet::where('address', '!=', 'genesis')->get();
}

function createBlock(int $height, string $publicKey, mixed $context = null)
{
    // dump('___CREATEBLOCK-'.$height.'-'.$publicKey);

    if ($context !== null) {
        $context->travel(Network::blockTime())->seconds();
    }

    $block = Block::factory()->create([
        'timestamp'              => Timestamp::now()->unix(),
        'previous_block'         => $height - 1,
        'height'                 => $height,
        'number_of_transactions' => 0,
        'total_amount'           => 0,
        'total_fee'              => 0,
        'reward'                 => 2 * 1e8,
        'generator_public_key'   => $publicKey,
    ]);

    return $block;
}

function createRoundEntry(int $round, string $publicKey)
{
    $existingRound = Round::where('round', $round)
        ->where('public_key', $publicKey)
        ->exists();

    if ($existingRound) {
        return;
    }

    $balance  = faker()->numberBetween(1, 1000) * 1e8;
    $existing = Round::firstWhere('public_key', $publicKey);
    if ($existing) {
        $balance = $existing->balance;
    }

    Round::factory()->create([
        'public_key' => $publicKey,
        'round'      => $round,
        'balance'    => $balance,
    ]);
}

// The initial delegate wallets are used as an index for the performances.
// This is to ensure the SAME delegate misses or forges per round
function createRealisticRound(array $performances, $context, bool $cachePerformance = true)
{
    // dump('___CREATEREALISTICROUND');

    $cache = new WalletCache();

    // Create initial round
    $round  = Round::count() + 1;
    $height = (($round - 1) * Network::delegateCount()) + 1;
    if ($round === 1) {
        Round::truncate();
        Block::truncate();
        Wallet::truncate();

        $context->travel(-(Network::delegateCount() * 8 * (count($performances) + 1)))->seconds();




        $genesis = Wallet::factory()->create([
            'address' => 'genesis',
        ]);

        createBlock($height, $genesis->public_key, $context);

        $height++;




        $delegateWallets = Wallet::factory(Network::delegateCount())
            ->activeDelegate()
            ->create();
    } else {
        $delegateWallets = getDelegateWallets();

        expect($delegateWallets)->toHaveCount(Network::delegateCount());
    }

    foreach ($delegateWallets as $delegate) {
        createRoundEntry($round, $delegate->public_key);

        $cache->setDelegate($delegate->public_key, $delegate);
    }

    foreach ($delegateWallets as $delegate) {
        createBlock($height, $delegate->public_key, $context);

        $height++;

        if (($height - 1) % Network::delegateCount() === 0) {
            $round++;
        }
    }

    // Loop through performances and generate rounds for each - requires 51 entries (blocks per round) to work correctly
    foreach ($performances as $index => $didForge) {
        createFullRound($round, $height, $delegateWallets, $context, $didForge);

        // expect(Block::count())->toBe(Network::delegateCount() + (($index + 1) * Network::delegateCount()) + 1);
        // expect(Block::count())->toBe(Network::delegateCount() + (($index + 1) * Network::delegateCount()));
    }

    (new NetworkCache())->setHeight(fn (): int => $height - 1);

    if ($cachePerformance) {
        (new CacheDelegatePerformance())->handle();
    }

    return [$delegateWallets, $round, $height];
}

function createFullRound(&$round, &$height, $delegateWallets, $context, $didForge = null)
{
    // dump('___CREATEFULLROUND');

    foreach ($delegateWallets as $delegate) {
        createRoundEntry($round, $delegate->public_key);
    }

    $delegates = getRoundDelegates(false, $round);

    $blockCount = 0;
    while ($blockCount < Network::delegateCount()) {
        foreach ($delegates as $delegate) {
            $delegateIndex = $delegateWallets->search(fn ($wallet) => $wallet->public_key === $delegate['publicKey']);
            if ($didForge && isset($didForge[$delegateIndex]) && $didForge[$delegateIndex] === false) {
                $context->travel(Network::blockTime())->seconds();

                continue;
            }

            createBlock($height + $blockCount, $delegate['publicKey'], $context);

            $blockCount++;

            if (($height + $blockCount - 1) % Network::delegateCount() === 0) {
                $round++;
            }

            if ($blockCount === Network::delegateCount()) {
                break;
            }
        }
    }

    $height += $blockCount;
}

function createPartialRound(
    int &$round,
    int &$height,
    ?int $blocks,
    $context,
    array $missedPublicKeys = [],
    array $requiredPublicKeys = [],
    bool $cachePerformance = true,
    ?int $slots = null,
) {
    // dump('___CREATEPARTIALROUND');

    foreach (getDelegateWallets() as $delegate) {
        createRoundEntry($round, $delegate->public_key);
    }

    $delegates = getRoundDelegates(false, $round);

    // dump($delegates->pluck('publicKey')->toArray());

    if (count($missedPublicKeys) > 0) {
        $hasPublicKey = false;
        foreach ($delegates as $delegate) {
            if (! in_array($delegate['publicKey'], $missedPublicKeys, true)) {
                continue;
            }

            $hasPublicKey = true;

            break;
        }

        if (! $hasPublicKey) {
            throw new \Exception('Missed Public Key is not in list of delegates');
        }
    }

    $requiredIndexes = [];
    if (count($requiredPublicKeys) > 0) {
        foreach ($delegates as $index => $delegate) {
            if (! in_array($delegate['publicKey'], $requiredPublicKeys, true)) {
                continue;
            }

            $requiredIndexes[] = $index;
        }
    }

    $slotCount  = 0;
    $blockCount = 0;
    while ($blockCount < Network::delegateCount()) {
        foreach ($delegates as $delegate) {
            if ($blocks !== null && $blockCount === $blocks) {
                break 2;
            }

            if ($slots !== null && $slotCount === $slots) {
                break 2;
            }

            if (count($missedPublicKeys) > 0 && in_array($delegate['publicKey'], $missedPublicKeys, true)) {
                $context->travel(8)->seconds();
                $slotCount++;

                continue;
            }

            createBlock($height + $blockCount, $delegate['publicKey'], $context);

            $context->travel(8)->seconds();

            $blockCount++;

            // if (($height + $blockCount - 1) % Network::delegateCount() === 0) {
            if ($blockCount === Network::delegateCount()) {
                $round++;

                // break 2;
            }

            $slotCount++;
        }
    }

    $height += $blockCount;

    (new NetworkCache())->setHeight(fn (): int => $height - 1);

    if ($cachePerformance) {
        (new CacheDelegatePerformance())->handle();
    }

    return [
        $delegates,
        $round,
        $height,
    ];
}

// function getHighestDelegateForgingPosition(int $round, array $publicKeys)
// {
//     // dump('___GETHIGHESTDELEGATEFORGINGPOSITION');

//     return getRoundDelegates(false, $round)
//         ->pluck('publicKey')
//         ->filter(fn ($publicKey) => in_array($publicKey, $publicKeys, true))
//         ->flip()
//         ->last();

//     $delegates    = getRoundDelegates(false, $round);
//     $highestIndex = null;
//     foreach ($delegates as $index => $delegate) {
//         if (! in_array($delegate['publicKey'], $publicKeys, true)) {
//             continue;
//         }

//         if ($highestIndex === null || $index > $highestIndex) {
//             $highestIndex = $index;
//         }
//     }

//     // dd($highestIndex);

//     return $highestIndex;
// }

// function getDelegateForgingPosition(int $round, string $publicKey)
// {
//     // dump('___GETDELEGATEFORGINGPOSITION');

//     return getRoundDelegates(false, $round)
//         ->search(fn ($delegate) => $delegate['publicKey'] === $publicKey);
// }

function getRoundDelegates(bool $withBlock = true, int $roundNumber = null): SupportCollection
{
    // dump('___GETROUNDDELEGATES');

    $round     = $roundNumber;
    $delegates = null;
    if ($roundNumber) {
        $delegates = Rounds::allByRound($roundNumber);
    }

    if (! $round || $delegates->count() === 0) {
        $rounds = Round::query()
            ->orderBy('round', 'desc')
            ->orderBy('balance', 'desc')
            ->orderBy('public_key', 'asc')
            ->limit(Network::delegateCount())
            ->get();

        $publicKeys = collect($rounds->pluck('public_key'));

        $delegates = Wallet::whereIn('public_key', $publicKeys)->get();

        $round = $rounds->first()->round;
    }

    if ($delegates->count() === 0) {
        $delegates = getDelegateWallets();
    }

    expect($delegates->count())->toBe(Network::delegateCount());

    $round       = $round ?: Rounds::current();
    $heightRange = Monitor::heightRangeByRound($round);

    // dump('round', $heightRange);

    try {
        dump('heightRange', $heightRange);
        $delegates = new SupportCollection(DelegateTracker::execute($delegates, $heightRange[0]));
        // dump('DELES', $delegates->map(fn ($d) => $d['publicKey'].'-'.$d['status'])->toArray());
    } catch (\Throwable $e) {
        dd($e);
        $delegates = $delegates->map(fn ($delegate) => [
            'publicKey' => $delegate,
            'status'    => 'initial',
        ]);
    }

    if ($withBlock) {
        $blocks = Block::whereBetween('height', $heightRange)->get()->keyBy('generator_public_key');

        $delegates = $delegates->map(fn ($delegate) => [
            ...$delegate,

            'block' => $blocks->get($delegate),
        ]);
    }

    return $delegates;
}
