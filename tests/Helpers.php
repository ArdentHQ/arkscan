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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
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

function createBlock(int $height, string $publicKey)
{
    Block::factory()->create([
        'timestamp'              => Timestamp::now()->unix(),
        'previous_block'         => $height - 1,
        'height'                 => $height,
        'number_of_transactions' => 0,
        'total_amount'           => 0,
        'total_fee'              => 0,
        'reward'                 => 2 * 1e8,
        'generator_public_key'   => $publicKey,
    ]);
}

function createRoundEntry(int $round, string $publicKey)
{
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
function createRealisticRound(array $performances, $context)
{
    $cache = new WalletCache();

    // Create initial round
    $round  = (Block::count() / Network::delegateCount()) + 1;
    $height = (($round - 1) * Network::delegateCount()) + 1;
    if ($round === 1) {
        Round::truncate();
        Block::truncate();
        Wallet::truncate();

        $context->travel(-(Network::delegateCount() * 8 * (count($performances) + 1)))->seconds();

        $delegateWallets = Wallet::factory(Network::delegateCount())
            ->activeDelegate()
            ->create()
            ->each(function ($delegate) use (&$height, $cache, $round) {
                $cache->setDelegate($delegate->public_key, $delegate);

                createRoundEntry($round, $delegate->public_key);

                createBlock($height, $delegate->public_key);

                $height++;
            });

        $round++;
    } else {
        $delegateWallets = Wallet::all();
    }

    expect($delegateWallets)->toHaveCount(Network::delegateCount());

    // Loop through performances and generate rounds for each - requires 51 entries (blocks per round) to work correctly
    foreach ($performances as $didForge) {
        createFullRound($round, $height, $delegateWallets, $context, $didForge);
    }

    expect($round - 1)->toBe(Block::count() / Network::delegateCount());
    expect(Block::count())->toBe(($round - 1) * Network::delegateCount());
    expect($height - 1)->toBe(($round - 1) * Network::delegateCount());

    (new NetworkCache())->setHeight(fn (): int => $height - 1);

    (new CacheDelegatePerformance())->handle();

    return [$delegateWallets, $round, $height];
}

function createFullRound(&$round, &$height, $delegateWallets, $context, $didForge = null)
{
    $delegates = delegatesForRound(false, $round);

    foreach (Wallet::all() as $delegate) {
        createRoundEntry($round, $delegate->public_key);
    }

    $blockCount = 0;
    while ($blockCount < 51) {
        foreach ($delegates as $delegate) {
            $delegateIndex = $delegateWallets->search(fn ($wallet) => $wallet->public_key === $delegate['publicKey']);
            if ($didForge && isset($didForge[$delegateIndex]) && ! $didForge[$delegateIndex]) {
                $context->travel(8)->seconds();

                continue;
            }

            createBlock($height + $blockCount, $delegate['publicKey']);

            $context->travel(8)->seconds();

            $blockCount++;
            if ($blockCount === 51) {
                break;
            }
        }
    }

    $round++;
    $height += $blockCount;
}

function createPartialRound(int &$round, int &$height, int $blocks, $context, string $requiredPublicKey = null, string $missedPublicKey = null)
{
    $delegates = delegatesForRound(false, $round);

    foreach (Wallet::all() as $delegate) {
        createRoundEntry($round, $delegate->public_key);
    }

    if ($missedPublicKey) {
        $hasPublicKey = getDelegateForgingPosition($round, $missedPublicKey) !== false;
        if (! $hasPublicKey) {
            throw new \Exception('Missed Public Key is not in list of delegates');
        }
    }

    $requiredIndex = null;
    if ($requiredPublicKey) {
        $requiredIndex = getDelegateForgingPosition($round, $requiredPublicKey);
    }

    $round++;

    $blockCount = 0;
    while ($blockCount < 51) {
        foreach ($delegates as $delegate) {
            if ($blockCount === $blocks) {
                break 2;
            }

            if ($missedPublicKey && $delegate['publicKey'] === $missedPublicKey) {
                $context->travel(8)->seconds();

                continue;
            }

            createBlock($height + $blockCount, $delegate['publicKey']);

            $context->travel(8)->seconds();

            $blockCount++;
        }
    }

    $height += $blockCount;

    if ($requiredIndex && ($requiredIndex === 50 || $requiredIndex >= $blocks)) {
        Artisan::call('cache:clear');

        return createPartialRound($round, $height, $blocks, $context, $missedPublicKey, $requiredPublicKey);
    }

    (new NetworkCache())->setHeight(fn (): int => $height - 1);

    (new CacheDelegatePerformance())->handle();
}

function getDelegateForgingPosition(int $round, string $publicKey)
{
    return delegatesForRound(false, $round)
        ->search(fn ($delegate) => $delegate['publicKey'] === $publicKey);
}

function delegatesForRound(bool $withBlock = true, int $roundNumber = null): SupportCollection
{
    $delegates = null;
    if ($roundNumber) {
        $delegates = Rounds::allByRound($roundNumber);
    }

    if (! $delegates || $delegates->count() === 0) {
        $delegates = Round::query()
            ->orderBy('round')
            ->orderBy('balance', 'desc')
            ->orderBy('public_key', 'asc')
            ->limit(51)
            ->get();
    }

    if ($delegates->count() === 0) {
        $delegates = Wallet::all();
    }

    expect($delegates->count())->toBe(51);

    $roundNumber = $roundNumber ?: Rounds::current();
    $heightRange = Monitor::heightRangeByRound($roundNumber);

    try {
        $delegates = new SupportCollection(DelegateTracker::execute($delegates, $heightRange[0]));
    } catch (\Throwable) {
        $delegates = $delegates->map(fn ($delegate) => [
            'publicKey' => $delegate->public_key,
            'status'    => 'initial',
        ]);
    }

    if ($withBlock) {
        $blocks = Block::whereBetween('height', $heightRange)->get()->keyBy('generator_public_key');

        $delegates = $delegates->map(fn ($delegate) => [
            ...$delegate,

            'block' => $blocks->get($delegate['publicKey'] ?? $delegate['public_key']),
        ]);
    }

    return $delegates;
}

function mockTaggedCache($withTags = false)
{
    $taggedCache = Cache::tags('tags');

    $mockedCache = Cache::shouldReceive('driver')
        ->andReturn($taggedCache);

    if ($withTags) {
        $mockedCache
            ->shouldReceive('tags')
            ->andReturn($taggedCache);
    }

    return $mockedCache;
}
