<?php

declare(strict_types=1);

namespace Tests;

use App\Console\Commands\CacheDelegatePerformance;
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

// The initial delegate wallets are used as an index for the performances.
// This is to ensure the SAME delegate misses or forges per round
function createRealisticRound(array $performances, $context)
{
    Round::truncate();
    Block::truncate();

    $context->travel(-(51 * 8 * (count($performances) + 1)))->seconds();

    $height = 1;
    $cache  = new WalletCache();

    // Create initial round
    $round           = 1;
    $delegateWallets = Wallet::factory(51)
        ->activeDelegate()
        ->create()
        ->each(function ($delegate, $index) use (&$height, $cache, $round) {
            $cache->setDelegate($delegate->public_key, $delegate);

            Round::factory()->create([
                'public_key' => $delegate->public_key,
                'round'      => $round,
                'balance'    => ($index + 50000) * 1e8,
            ]);

            createBlock($height, $delegate->public_key);

            $height++;
        });

    expect(Block::count())->toBe(51);
    // expect(Block::count())->toBe($height - 1);

    // Loop through performances and generate rounds for each - requires 51 entries (blocks per round) to work correctly
    $round++;
    foreach ($performances as $index => $didForge) {
        $delegates = delegatesForRound();
        foreach ($delegates as $delegate) {
            Round::factory()->create([
                'public_key' => $delegate['publicKey'],
                'balance'    => 0,
                'round'      => $round,
            ]);
        }

        $blockCount = 0;
        while ($blockCount < 51) {
            foreach ($delegates as $delegate) {
                $delegateIndex = $delegateWallets->search(fn ($wallet) => $wallet->public_key === $delegate['publicKey']);
                if (isset($didForge[$delegateIndex]) && ! $didForge[$delegateIndex]) {
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

        expect(Block::count())->toBe(51 + (($index + 1) * 51));
        // expect(Block::count())->toBe($height - 1);
    }

    (new NetworkCache())->setHeight(fn (): int => $height - 1);

    (new CacheDelegatePerformance())->handle();

    return [$delegateWallets, $round, $height];
}

function createPartialRound(int &$round, int &$height, int $blocks, $context, string $missedPublicKey = null, string $requiredPublicKey = null)
{
    $delegates = delegatesForRound(false, $round);
    dump('Round: '.$round, Rounds::current());
    dump($delegates->map(function ($d) {
        return $d['status'].'-'.(array_key_exists('block', $d) && $d['block'] ? $d['block']['height'].'-' : '').(array_key_exists('block', $d) && $d['block'] ? 'true' : 'false').'-'.$d['publicKey'];
    }));

    if ($missedPublicKey) {
        $hasPublicKey = false;
        foreach ($delegates as $delegate) {
            if ($delegate['publicKey'] !== $missedPublicKey) {
                continue;
            }

            $hasPublicKey = true;

            break;
        }

        if (! $hasPublicKey) {
            throw new \Exception('Missed Public Key is not in list of delegates');
        }
    }

    $requiredIndex = null;
    if ($requiredPublicKey) {
        foreach ($delegates as $index => $delegate) {
            if ($delegate['publicKey'] !== $requiredPublicKey) {
                continue;
            }

            $requiredIndex = $index;

            break;
        }

        if ($requiredIndex >= $blocks) {
            $blocks = $requiredIndex;
        }
    }

    foreach ($delegates as $delegate) {
        Round::factory()->create([
            'public_key' => $delegate['publicKey'],
            'balance'    => 0,
            'round'      => $round,
        ]);
    }

    $round++;

    $blockCount = 0;
    foreach ($delegates as $delegate) {
        if ($blockCount === $blocks) {
            break;
        }

        if ($missedPublicKey && $delegate['publicKey'] === $missedPublicKey) {
            dump($delegate['publicKey'].'-missed');
            $context->travel(8)->seconds();

            continue;
        }

        dump($delegate['publicKey'].'-forged');

        createBlock($height + $blockCount, $delegate['publicKey']);

        $context->travel(8)->seconds();

        $blockCount++;
    }

    $height += $blockCount;

    // expect(Block::count())->toBe($height - 1);

    if ($requiredIndex && $requiredIndex === 50) {
        return createPartialRound($round, $height, $blocks, $context, $missedPublicKey, $requiredPublicKey);
    }

    (new NetworkCache())->setHeight(fn (): int => $height - 1);

    (new CacheDelegatePerformance())->handle();
}

function delegatesForRound(bool $withBlock = true, int $roundNumber = null): SupportCollection
{
    $delegates = Round::query()
        ->orderBy('balance', 'desc')
        ->orderBy('public_key', 'asc')
        ->get();

    dump('ASDASDASD', $roundNumber);
    $roundNumber = $roundNumber ?: Rounds::current();
    $heightRange = Monitor::heightRangeByRound($roundNumber);
    $delegates   = new SupportCollection(DelegateTracker::execute($delegates, $heightRange[0]));
    dump($heightRange);
    if ($withBlock) {
        $blocks = Block::whereBetween('height', $heightRange)->get()->keyBy('generator_public_key');

        $delegates = $delegates->map(fn ($delegate) => [
            ...$delegate,

            'block' => $blocks->get($delegate['publicKey']),
        ]);
    }

    return $delegates;
}
