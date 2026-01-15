<?php

declare(strict_types=1);

use App\Enums\StatsPeriods;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\FeeCache;
use App\Services\Cache\MainsailCache;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\StatisticsCache;
use App\Services\Cache\TransactionCache;
use App\Services\Cache\ValidatorCache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Cache::tags('network')->flush();
    Cache::tags('transaction')->flush();
    Cache::tags('fee')->flush();
    Cache::tags('statistics')->flush();
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
