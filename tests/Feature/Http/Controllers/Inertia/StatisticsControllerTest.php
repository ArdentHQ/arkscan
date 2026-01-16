<?php

declare(strict_types=1);

use App\Enums\StatsPeriods;
use App\Facades\Network;
use App\Facades\Settings;
use App\Http\Controllers\Inertia\StatisticsController;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\BlockCache;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\FeeCache;
use App\Services\Cache\MainsailCache;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\Cache\StatisticsCache;
use App\Services\Cache\TransactionCache;
use App\Services\Cache\ValidatorCache;
use App\Services\Cache\WalletCache;
use App\Services\NumberFormatter;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Brick\Math\BigDecimal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Cache::tags('block')->flush();
    Cache::tags('crypto_data')->flush();
    Cache::tags('network')->flush();
    Cache::tags('network_status_block')->flush();
    Cache::tags('transaction')->flush();
    Cache::tags('fee')->flush();
    Cache::tags('statistics')->flush();
    Cache::tags('wallet')->flush();
    Cache::tags('mainsail')->flush();
});

it('should render the page without any errors', function () {
    $this
        ->get(route('statistics'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Statistics/Index')
            ->has('gasTracker')
            ->has('highlights')
            ->has('informationCards')
            ->has('insights'));
});

it('should include highlights and gas tracker data', function () {
    Config::set('arkscan.networks.development.canBeExchanged', false);

    Wallet::factory()->count(5)->create();

    $networkCache = new NetworkCache();
    $networkCache->setSupply(function (): float {
        return 12345.6789 * 1e18;
    });
    $networkCache->setVotesPercentage('42.5');
    $networkCache->setValidatorRegistrationCount(12);

    (new ValidatorCache())->setTotalBalanceVoted(1234.567);
    (new MainsailCache())->setFees([
        'min' => '1500000000',
        'avg' => '2500000000',
        'max' => '3500000000',
    ]);

    $this
        ->get(route('statistics'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Statistics/Index')
            ->where('highlights.totalSupply', '12,345.679')
            ->where('highlights.voting.percentage', '42.50%')
            ->where('highlights.validators', '12')
            ->where('highlights.wallets', '5')
            ->where('gasTracker.fees.low.amount', '1.5')
            ->where('gasTracker.fees.average.amount', '2.5')
            ->where('gasTracker.fees.high.amount', '3.5'));
});

it('should include information card data', function () {
    $transactionCache = new TransactionCache();
    $feeCache         = new FeeCache();

    foreach ([
        StatsPeriods::DAY,
        StatsPeriods::WEEK,
        StatsPeriods::MONTH,
        StatsPeriods::QUARTER,
        StatsPeriods::YEAR,
        StatsPeriods::ALL,
    ] as $period) {
        $transactionCache->setHistorical($period, collect([
            '2024-01' => 2,
            '2024-02' => 3,
        ]));

        $feeCache->setHistorical($period, collect([
            '2024-01' => 1 * 1e18,
            '2024-02' => 2 * 1e18,
        ]));
    }

    $this
        ->get(route('statistics'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Statistics/Index')
            ->where('informationCards.transactions.allTimeValue', '5')
            ->where('informationCards.fees.allTimeValue', '3 DARK'));
});

it('should include insights data', function () {
    $transaction = Transaction::factory()->create([
        'timestamp' => Carbon::parse('2024-01-01 00:00:00')->getTimestampMs(),
        'value'     => 2 * 1e18,
    ]);

    $transactionCache = new TransactionCache();
    $transactionCache->setHistoricalByType('transfer', 42);
    $transactionCache->setLargestIdByAmount($transaction->hash);

    $statisticsCache = new StatisticsCache();
    $statisticsCache->setGenesisAddress([
        'address' => 'GENESIS',
        'value'   => Carbon::parse('2024-01-01')->format('d M Y'),
    ]);

    $this
        ->get(route('statistics'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Statistics/Index')
            ->where('insights.transactions.details.transfer', 42)
            ->where('insights.addresses.unique.genesis.address', 'GENESIS')
            ->where('insights.transactions.records.largest_transaction.hash', $transaction->hash));
});

it('should throw when chart cache is invalid', function () {
    $controller = new StatisticsController();

    // Access private chartData to assert invalid cache handling.
    $call = \Closure::bind(function () {
        return $this->chartData('invalid-cache', StatsPeriods::DAY);
    }, $controller, StatisticsController::class);

    expect(fn () => $call())->toThrow(\InvalidArgumentException::class);
});

it('should format fee cards above threshold and convert chart datasets', function () {
    $feeCache = new FeeCache();

    $aboveThreshold = 10001 * 1e18;

    foreach ([
        StatsPeriods::DAY,
        StatsPeriods::WEEK,
        StatsPeriods::MONTH,
        StatsPeriods::QUARTER,
        StatsPeriods::YEAR,
        StatsPeriods::ALL,
    ] as $period) {
        $feeCache->setHistorical($period, collect([
            '2024-01' => $aboveThreshold,
        ]));
    }

    $convertedAmount = NumberFormatter::weiToArk((string) BigDecimal::of($aboveThreshold), false);
    $expectedValue   = sprintf('%s %s', NumberFormatter::number($convertedAmount), Network::currency());
    $expectedTooltip = NumberFormatter::currency(
        NumberFormatter::weiToArk((string) BigDecimal::of($aboveThreshold)),
        Network::currency(),
    );
    $expectedDataset = BigDecimal::of(NumberFormatter::weiToArk((string) BigDecimal::of($aboveThreshold), false))->toFloat();

    $this
        ->get(route('statistics'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Statistics/Index')
            ->where('informationCards.fees.periods.day.value', $expectedValue)
            ->where('informationCards.fees.periods.day.tooltip', $expectedTooltip)
            ->where('informationCards.fees.periods.day.chart.datasets.0', fn ($value) => (float) $value === $expectedDataset));
});

it('should include market data, validators, addresses, annual data, and block records', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    $mostUniqueVoters  = Wallet::factory()->create(['attributes' => ['username' => 'most-voters']]);
    $leastUniqueVoters = Wallet::factory()->create(['attributes' => ['username' => 'least-voters']]);
    $mostBlocksForged  = Wallet::factory()->create([
        'attributes' => [
            'username'                => 'most-blocks',
            'validatorProducedBlocks' => 77,
        ],
    ]);
    $oldestActive = Wallet::factory()->create(['attributes' => ['username' => 'oldest']]);
    $newestActive = Wallet::factory()->create(['attributes' => ['username' => 'newest']]);

    $walletCache = new WalletCache();
    $walletCache->setVoterCount($mostUniqueVoters->address, 15);
    $walletCache->setVoterCount($leastUniqueVoters->address, 3);

    $statisticsCache = new StatisticsCache();
    $statisticsCache->setMostUniqueVoters($mostUniqueVoters->address);
    $statisticsCache->setLeastUniqueVoters($leastUniqueVoters->address);
    $statisticsCache->setMostBlocksForged($mostBlocksForged->address);
    $statisticsCache->setOldestActiveValidator($oldestActive->address, Carbon::parse('2020-01-01')->timestamp);
    $statisticsCache->setNewestActiveValidator($newestActive->address, Carbon::parse('2021-01-01')->timestamp);

    $statisticsCache->setAddressHoldings([
        ['grouped' => 0, 'count' => 2],
        ['grouped' => 1, 'count' => 3],
        ['grouped' => 1000, 'count' => 4],
    ]);

    $statisticsCache->setGenesisAddress([
        'address' => $mostUniqueVoters->address,
        'value'   => Carbon::parse('2024-01-01')->format(DateFormat::DATE),
    ]);
    $statisticsCache->setNewestAddress([
        'address' => $leastUniqueVoters->address,
        'value'   => Carbon::parse('2024-01-02')->format(DateFormat::DATE),
    ]);
    $statisticsCache->setMostTransactions([
        'address' => $mostBlocksForged->address,
        'value'   => 42,
    ]);

    $largestValue = 12345.678;
    $statisticsCache->setLargestAddress([
        'address' => $oldestActive->address,
        'value'   => $largestValue,
    ]);

    $startYear   = Carbon::parse(Network::epoch())->year;
    $currentYear = Carbon::now()->year;
    $statisticsCache->setAnnualData($startYear, 10, '100', '0.5', 2);
    if ($currentYear !== $startYear) {
        $statisticsCache->setAnnualData($currentYear, 20, '200', '1.5', 3);
    }

    $currency = Settings::currency();
    $statisticsCache->setPriceRangeDaily($currency, 1.1, 2.2);
    $statisticsCache->setPriceRange52($currency, 0.9, 3.3);
    $statisticsCache->setPriceAtl($currency, Carbon::parse('2020-01-01')->timestamp, 0.5);
    $statisticsCache->setPriceAth($currency, Carbon::parse('2021-01-01')->timestamp, 5.5);
    $statisticsCache->setVolumeAtl($currency, Carbon::parse('2020-02-01')->timestamp, 10.5);
    $statisticsCache->setVolumeAth($currency, Carbon::parse('2021-02-01')->timestamp, 20.5);
    $statisticsCache->setMarketCapAtl($currency, Carbon::parse('2020-03-01')->timestamp, 100.5);
    $statisticsCache->setMarketCapAth($currency, Carbon::parse('2021-03-01')->timestamp, 200.5);

    (new CryptoDataCache())->setVolume($currency, '12345');
    (new NetworkStatusBlockCache())->setPrice(Network::currency(), $currency, 2.5);
    (new NetworkCache())->setSupply(function (): float {
        return 100 * 1e18;
    });

    $transaction = Transaction::factory()->create([
        'timestamp' => Carbon::parse('2024-01-03 00:00:00')->getTimestampMs(),
        'value'     => 3 * 1e18,
    ]);

    $highestFeeBlock = Block::factory()->create([
        'fee'        => 2 * 1e18,
        'timestamp'  => Carbon::parse('2024-01-04')->getTimestampMs(),
    ]);
    $mostTransactionsBlock = Block::factory()->create([
        'transactions_count' => 99,
        'timestamp'          => Carbon::parse('2024-01-05')->getTimestampMs(),
    ]);

    (new TransactionCache())->setLargestIdByAmount($transaction->hash);

    $blockCache = new BlockCache();
    $blockCache->setLargestIdByFees($highestFeeBlock->hash);
    $blockCache->setLargestIdByTransactionCount($mostTransactionsBlock->hash);

    $expectedHighestFee = NumberFormatter::currencyWithDecimals(
        $highestFeeBlock->fee->toFloat(),
        Network::currency(),
        2,
    );
    $expectedLargestShort = NumberFormatter::currencyShort($largestValue, Network::currency());
    $expectedLargestFull  = NumberFormatter::currencyWithDecimals($largestValue, Network::currency(), 2);
    $expectedLowFees      = NumberFormatter::currencyWithDecimals('0.5', Network::currency(), 4);

    $this
        ->get(route('statistics'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Statistics/Index')
            ->where('insights.marketData.prices.daily.low', NumberFormatter::currencyWithDecimals(1.1, $currency, 2))
            ->where('insights.validators.0.key', 'most_unique_voters')
            ->where('insights.validators.0.value', 15)
            ->where('insights.validators.2.key', 'oldest_active_validator')
            ->where('insights.validators.2.value', Carbon::parse('2020-01-01')->format(DateFormat::DATE))
            ->where('insights.validators.4.key', 'most_blocks_forged')
            ->where('insights.validators.4.value', 77)
            ->where('insights.transactions.records.highest_fee.fee', $expectedHighestFee)
            ->where('insights.transactions.records.most_transactions_in_block.transactionCount', 99)
            ->where('insights.addresses.holdings.0.grouped', 1)
            ->where('insights.addresses.holdings.0.count', 7)
            ->where('insights.addresses.unique.largest.valueShort', $expectedLargestShort)
            ->where('insights.addresses.unique.largest.valueFull', $expectedLargestFull)
            ->where('insights.annual.0.fees', $expectedLowFees));
});
