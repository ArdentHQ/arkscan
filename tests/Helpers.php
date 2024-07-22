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
use Illuminate\Support\Facades\Artisan;
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
                'address' => '0xC5a19e23E99bdFb7aae4301A009763AdC01c1b5B',
            ], [
                'type'    => 'exchange',
                'name'    => 'Binance',
                'address' => '0xEd0C906b8fcCDe71A19322DFfe929c6e04460cFF',
            ], [
                'type'    => 'exchange',
                'name'    => 'Altilly',
                'address' => '0xe7dd7E34d2F24966C3C7AA89FC30ACA65760F6B5',
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
    return Block::factory()->create([
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
    if (Round::where('round', $round)->exists()) {
        return;
    }

    Round::factory()->create([
        'round'        => $round,
        'round_height' => $height,
        'validators'   => $wallets->pluck('public_key'),
    ]);
}

// The initial validator wallets are used as an index for the performances.
// This is to ensure the SAME validator misses or forges per round
function createRealisticRound(array $performances, $context, bool $cachePerformance = true)
{
    $cache = new WalletCache();

    // Create initial round
    $round  = Round::count() + 1;
    $height = (($round - 1) * Network::validatorCount()) + 1;
    if ($round === 1) {
        Round::truncate();
        Block::truncate();
        Wallet::truncate();

        $context->travel(-(Network::validatorCount() * 8 * (count($performances) + 1)))->seconds();
    }

    $validatorWallets = Wallet::factory(Network::validatorCount())
        ->activeValidator()
        ->create();

    createRoundEntry($round, $height, $validatorWallets);

    $validatorWallets->each(function ($validator) use (&$height, $cache) {
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

    if ($cachePerformance) {
        (new CacheValidatorPerformance())->handle();
    }

    return [$validatorWallets, $round, $height];
}

function createFullRound(&$round, &$height, $validatorWallets, $context, $didForge = null)
{
    createRoundEntry($round, $height, Wallet::all());
    $validators = getRoundValidators(false, $round);

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
    createRoundEntry($round, $height, Wallet::all());
    $validators = getRoundValidators(false, $round);

    if (count($missedPublicKeys) > 0) {
        $hasPublicKey = false;
        foreach ($validators as $validator) {
            if (! in_array($validator['publicKey'], $missedPublicKeys, true)) {
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
    if ($requiredPublicKeys) {
        foreach ($validators as $index => $validator) {
            if (! in_array($validator['publicKey'], $requiredPublicKeys, true)) {
                continue;
            }

            $requiredIndex = $index;

            break;
        }
    }

    $round++;

    $slotCount  = 0;
    $blockCount = 0;
    while ($blockCount < Network::validatorCount()) {
        foreach ($validators as $validator) {
            if ($blocks !== null && $blockCount === $blocks) {
                break 2;
            }

            if ($slots !== null && $slotCount === $slots) {
                break 2;
            }

            if (count($missedPublicKeys) > 0 && in_array($validator['publicKey'], $missedPublicKeys, true)) {
                $context->travel(8)->seconds();
                $slotCount++;

                continue;
            }

            createBlock($height + $blockCount, $validator['publicKey']);

            $context->travel(8)->seconds();

            $blockCount++;
            $slotCount++;
        }
    }

    $height += $blockCount;

    if ($requiredIndex && ($requiredIndex === Network::validatorCount() - 1 || ($blocks !== null && $requiredIndex >= $blocks))) {
        Artisan::call('cache:clear');

        return createPartialRound($round, $height, $blocks, $context, $missedPublicKeys, $requiredPublicKeys, $cachePerformance, $slots);
    }

    (new NetworkCache())->setHeight(fn (): int => $height - 1);

    if ($cachePerformance) {
        (new CacheValidatorPerformance())->handle();
    }
}

function getRoundValidators(bool $withBlock = true, int $roundNumber = null): SupportCollection
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
