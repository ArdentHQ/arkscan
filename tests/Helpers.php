<?php

declare(strict_types=1);

namespace Tests;

use App\Console\Commands\CacheValidatorPerformance;
use App\Facades\Network;
use App\Facades\Rounds;
use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\WalletCache;
use App\Services\Monitor\Monitor;
use App\Services\Monitor\ValidatorTracker;
use App\Services\Timestamp;
use ArkEcosystem\Crypto\Identities\PublicKey;
use Faker\Generator;
use FurqanSiddiqui\BIP39\BIP39;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\Stubs\FullPartialRoundException;

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
        'timestamp'              => Timestamp::now()->getTimestampMs(),
        'previous_block'         => $height - 1,
        'height'                 => $height,
        'number_of_transactions' => 0,
        'total_amount'           => 0,
        'total_fee'              => 0,
        'reward'                 => 2 * 1e8,
        'generator_public_key'   => $publicKey,
    ]);
}

function createRoundEntry(int $round, int $height, SupportCollection $wallets)
{
    Round::factory()->create([
        'round'        => $round,
        'round_height' => $height,
        'validators'   => $wallets->pluck('public_key'),
    ]);
}

// The initial validator wallets are used as an index for the performances.
// This is to ensure the SAME validator misses or forges per round
function createRealisticRound(array $performances, $context)
{
    Round::truncate();
    Block::truncate();
    Wallet::truncate();

    $context->travel(-(Network::validatorCount() * 8 * (count($performances) + 1)))->seconds();

    $height = 1;
    $cache  = new WalletCache();

    // Create initial round
    $round            = 1;
    $validatorWallets = Wallet::factory(Network::validatorCount())
        ->activeValidator()
        ->create();

    createRoundEntry($round, $height, $validatorWallets);

    $validatorWallets->each(function ($validator) use (&$height, $cache, $round) {
        $cache->setValidator($validator->public_key, $validator);

        createBlock($height, $validator->public_key);

        $height++;
    });

    $round++;

    expect(Block::count())->toBe(Network::validatorCount());
    expect($height - 1)->toBe(Network::validatorCount());

    // Loop through performances and generate rounds for each - requires validator count entries (blocks per round) to work correctly
    foreach ($performances as $index => $didForge) {
        createFullRound($round, $height, $validatorWallets, $context, $didForge);

        expect(Block::count())->toBe(Network::validatorCount() + (($index + 1) * Network::validatorCount()));
    }

    (new NetworkCache())->setHeight(fn (): int => $height - 1);

    (new CacheValidatorPerformance())->handle();

    return [$validatorWallets, $round, $height];
}

function createFullRound(&$round, &$height, $validatorWallets, $context, $didForge = null)
{
    createRoundEntry($round, $height, Wallet::all());
    $validators = validatorCount(false, $round);

    $blockCount = 0;
    while ($blockCount < Network::validatorCount()) {
        foreach ($validators as $validator) {
            $validatorIndex = $validatorWallets->search(fn ($wallet) => $wallet->public_key === $validator['publicKey']);
            if ($didForge && isset($didForge[$validatorIndex]) && ! $didForge[$validatorIndex]) {
                $context->travel(8)->seconds();

                continue;
            }

            createBlock($height + $blockCount, $validator['publicKey']);

            $context->travel(8)->seconds();

            $blockCount++;
            if ($blockCount === Network::validatorCount()) {
                break;
            }
        }
    }

    $round++;
    $height += $blockCount;
}

function createPartialRound(int &$round, int &$height, int $blocks, $context, string $missedPublicKey = null, string $requiredPublicKey = null)
{
    createRoundEntry($round, $height, Wallet::all());
    $validators = validatorCount(false, $round);

    if ($missedPublicKey) {
        $hasPublicKey = false;
        foreach ($validators as $validator) {
            if ($validator['publicKey'] !== $missedPublicKey) {
                continue;
            }

            $hasPublicKey = true;

            break;
        }

        if (! $hasPublicKey) {
            throw new \Exception('Missed Public Key is not in list of validators');
        }
    }

    $requiredIndex = null;
    if ($requiredPublicKey) {
        foreach ($validators as $index => $validator) {
            if ($validator['publicKey'] !== $requiredPublicKey) {
                continue;
            }

            $requiredIndex = $index;

            break;
        }

        if ($requiredIndex >= $blocks) {
            throw new FullPartialRoundException();
        }
    }

    $round++;

    $blockCount = 0;
    while ($blockCount < Network::validatorCount()) {
        foreach ($validators as $validator) {
            if ($blockCount === $blocks) {
                break 2;
            }

            if ($missedPublicKey && $validator['publicKey'] === $missedPublicKey) {
                $context->travel(8)->seconds();

                continue;
            }

            createBlock($height + $blockCount, $validator['publicKey']);

            $context->travel(8)->seconds();

            $blockCount++;
        }
    }

    $height += $blockCount;

    if ($requiredIndex && $requiredIndex === 50) {
        return createPartialRound($round, $height, $blocks, $context, $missedPublicKey, $requiredPublicKey);
    }

    (new NetworkCache())->setHeight(fn (): int => $height - 1);

    (new CacheValidatorPerformance())->handle();
}

function validatorCount(bool $withBlock = true, int $roundNumber = null): SupportCollection
{
    $round      = null;
    $validators = null;
    if ($roundNumber) {
        $round      = Rounds::byRound($roundNumber);
        $validators = collect($round->validators);
    }

    if (! $round || $validators->count() === 0) {
        $round = Round::query()
            ->orderBy('round')
            ->orderBy('round', 'desc')
            ->first();

        $validators = collect($round->validators);
    }

    if ($validators->count() === 0) {
        $validators = Wallet::all()->pluck('public_key');
    }

    expect($validators->count())->toBe(Network::validatorCount());

    $round       = $round ?: Rounds::current();
    $heightRange = Monitor::heightRangeByRound($round);

    try {
        $validators = new SupportCollection(ValidatorTracker::execute($validators->toArray(), $heightRange[0]));
    } catch (\Throwable $e) {
        $validators = $validators->map(fn ($validator) => [
            'publicKey' => $validator,
            'status'    => 'initial',
        ]);
    }

    if ($withBlock) {
        $blocks = Block::whereBetween('height', $heightRange)->get()->keyBy('generator_public_key');

        $validators = $validators->map(fn ($validator) => [
            ...$validator,

            'block' => $blocks->get($validator),
        ]);
    }

    return $validators;
}
