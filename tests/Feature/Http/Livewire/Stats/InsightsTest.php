<?php

declare(strict_types=1);

use App\Console\Commands\CacheVolume;
use App\Contracts\Network as NetworkContract;
use App\DTO\Statistics\TransactionAveragesStatistics;
use App\DTO\Statistics\TransactionRecordsStatistics;
use App\DTO\Statistics\TransactionStatistics;
use App\Enums\StatsTransactionType;
use App\Facades\Settings;
use App\Http\Livewire\Stats\Insights;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Blockchain\Network as Blockchain;
use App\Services\Cache\BlockCache;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\Cache\StatisticsCache;
use App\Services\Cache\TransactionCache;
use App\Services\MarketDataProviders\CoinGecko;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\Feature\Http\Livewire\__stubs\NetworkStub;

it('should render transaction details', function (): void {
    Transaction::factory(12)->validatorRegistration()->create();
    Transaction::factory(13)->validatorResignation()->create();
    Transaction::factory(14)->transfer()->create();
    Transaction::factory(15)->vote()->create();
    Transaction::factory(16)->unvote()->create();
    Transaction::factory(17)->voteCombination()->create();
    Transaction::factory(18)->multipayment()->create();

    $transactionCache = new TransactionCache();
    $transactionCache->getCache()->flush();

    Artisan::call('explorer:cache-transactions');

    $largestTransaction         = Transaction::find($transactionCache->getLargestIdByAmount());
    $largestTransaction->amount = BigNumber::new($largestTransaction->amount->valueOf());

    $transactionDetails = TransactionStatistics::make(
        [
            'transfer'               => 14,
            'multipayment'           => 18,
            'vote'                   => 15,
            'unvote'                 => 16,
            'switch_vote'            => 17,
            'validator_registration' => 12,
            'validator_resignation'  => 13,
        ],
        TransactionAveragesStatistics::make($transactionCache->getHistoricalAverages()),
        TransactionRecordsStatistics::make($largestTransaction),
    );

    Livewire::test(Insights::class)
        ->assertViewHas('transactionDetails', $transactionDetails)
        ->assertSeeInOrder([
            trans('pages.statistics.insights.transactions.header.transfer'),
            '14',
            trans('pages.statistics.insights.transactions.header.multipayment'),
            '18',
            trans('pages.statistics.insights.transactions.header.vote'),
            '15',
            trans('pages.statistics.insights.transactions.header.unvote'),
            '16',
            trans('pages.statistics.insights.transactions.header.switch_vote'),
            '17',
            trans('pages.statistics.insights.transactions.header.validator_registration'),
            '12',
            trans('pages.statistics.insights.transactions.header.validator_resignation'),
            '13',
        ]);
});

it('should render transaction daily average', function (): void {
    $networkStub = new NetworkStub(true, Carbon::now()->subDay(2));
    app()->singleton(NetworkContract::class, fn () => $networkStub);

    $transactionCache = new TransactionCache();

    Transaction::factory(2)->validatorRegistration()->create([
        'amount' => 0,
        'fee'    => 9 * 1e8,
    ]);
    Transaction::factory(3)->transfer()->create([
        'amount' => 2000 * 1e8,
        'fee'    => 10 * 1e8,
    ]);
    Transaction::factory(4)->multipayment()->create([
        'amount' => 0,
        'fee'    => 11 * 1e8,
        'asset'  => [
            'payments' => [
                [
                    'amount' => 3000 * 1e8,
                ],
            ],
        ],
    ]);

    expect(Transaction::count())->toBe(9);

    $transactionCount = (int) round(9 / 2);
    $totalAmount      = (int) round(((4 * 3000) + (3 * 2000)) / 2);
    $totalFees        = (int) round(((9 * 2) + (10 * 3) + (11 * 4)) / 2);

    Artisan::call('explorer:cache-transactions');

    $transactionDetails = TransactionStatistics::make(
        StatsTransactionType::all()
            ->mapWithKeys(fn ($type) => [$type => $transactionCache->getHistoricalByType($type)])
            ->toArray(),
        TransactionAveragesStatistics::make($transactionCache->getHistoricalAverages()),
        TransactionRecordsStatistics::make(Transaction::find($transactionCache->getLargestIdByAmount())),
    );

    Livewire::test(Insights::class)
        ->assertViewHas('transactionDetails', $transactionDetails)
        ->assertSeeInOrder([
            trans('pages.statistics.insights.transactions.header.transfer'),
            trans('pages.statistics.insights.transactions.header.multipayment'),
            trans('pages.statistics.insights.transactions.header.vote'),
            trans('pages.statistics.insights.transactions.header.unvote'),
            trans('pages.statistics.insights.transactions.header.switch_vote'),
            trans('pages.statistics.insights.transactions.header.validator_registration'),
            trans('pages.statistics.insights.transactions.header.validator_resignation'),
            trans('pages.statistics.insights.transactions.header.transactions'),
            $transactionCount,
            trans('pages.statistics.insights.transactions.header.transaction_volume'),
            number_format($totalAmount).' DARK',
            trans('pages.statistics.insights.transactions.header.transaction_fees'),
            number_format($totalFees).' DARK',
        ]);
});

it('should render transaction records', function (): void {
    $largestTransaction        = Transaction::factory()->transfer()->create();
    $otherTransaction          = Transaction::factory()->transfer()->create();
    $largestBlock              = Block::factory()->create();
    $largestBlockFee           = Block::factory()->create();
    $blockWithMostTransactions = Block::factory()->create();
    $otherBlock                = Block::factory()->create();

    (new TransactionCache())->setLargestIdByAmount($largestTransaction->id);
    (new BlockCache())->setLargestIdByAmount($largestBlock->id);
    (new BlockCache())->setLargestIdByFees($largestBlockFee->id);
    (new BlockCache())->setLargestIdByTransactionCount($blockWithMostTransactions->id);

    Livewire::test(Insights::class)
        ->assertSeeInOrder([
            trans('pages.statistics.insights.transactions.header.largest_transaction'),
            $largestTransaction->id,
            trans('pages.statistics.insights.transactions.header.largest_block'),
            $largestBlock->id,
            trans('pages.statistics.insights.transactions.header.highest_fee'),
            $largestBlockFee->id,
            trans('pages.statistics.insights.transactions.header.most_transactions_in_block'),
            $blockWithMostTransactions->id,
        ])
        ->assertDontSee($otherTransaction->id)
        ->assertDontSee($otherBlock->id);
});

it('should render address holdings', function (): void {
    $holdings = [
        ['grouped' => 0, 'count' => 5],
        ['grouped' => 1, 'count' => 6],
        ['grouped' => 1000, 'count' => 3],
        ['grouped' => 10000, 'count' => 4],
        ['grouped' => 1000000, 'count' => 2],
    ];

    (new StatisticsCache())->setAddressHoldings($holdings);

    Livewire::test(Insights::class)
        ->assertSeeInOrder([
            '> 1',
            $holdings[4]['count'] + $holdings[3]['count'] + $holdings[2]['count'] + $holdings[1]['count'],
            '> 1,000',
            $holdings[4]['count'] + $holdings[3]['count'] + $holdings[2]['count'],
            '> 10,000',
            $holdings[4]['count'] + $holdings[3]['count'],
            '> 1,000,000',
            $holdings[4]['count'],
        ])
        ->assertDontSee('> 0');
});

it('should render unique addresses', function (): void {
    $currentDate = Carbon::now();

    $cache = new StatisticsCache();
    $cache->setGenesisAddress(['address' => 'address1', 'value' => $currentDate]);
    $cache->setNewestAddress(['address' => 'address2', 'value' => $currentDate]);
    $cache->setMostTransactions(['address' => 'address3', 'value' => 12345]);
    $cache->setLargestAddress(['address' => 'address4', 'value' => 789123]);

    Livewire::test(Insights::class)
        ->assertSeeInOrder([
            trans('pages.statistics.insights.addresses.header.genesis'),
            $currentDate,
            trans('pages.statistics.insights.addresses.header.newest'),
            $currentDate,
            trans('pages.statistics.insights.addresses.header.most_transactions'),
            '12,345',
            trans('pages.statistics.insights.addresses.header.largest'),
            '789,123',
        ])
        ->assertDontSee('> 0');
});

it('should render validator statistics', function (): void {
    $currentDate = Carbon::now();

    $walletMostUnique   = Wallet::factory()->activeValidator()->create();
    $walletLeastUnique  = Wallet::factory()->activeValidator()->create();
    $walletOldestActive = Wallet::factory()->activeValidator()->create();
    $walletNewestActive = Wallet::factory()->activeValidator()->create();
    $walletMostBlocks   = Wallet::factory()->activeValidator()->create();
    $randomWallet       = Wallet::factory()->activeValidator()->create();

    $cache = new StatisticsCache();
    $cache->setMostUniqueVoters($walletMostUnique->public_key);
    $cache->setLeastUniqueVoters($walletLeastUnique->public_key);
    $cache->setOldestActiveValidator($walletOldestActive->public_key, $currentDate->subMonth()->timestamp);
    $cache->setNewestActiveValidator($walletNewestActive->public_key, $currentDate->timestamp);
    $cache->setMostBlocksForged($walletMostBlocks->public_key);

    Livewire::test(Insights::class)
        ->assertSeeInOrder([
            // trans('pages.statistics.insights.validators.header.most_unique_voters'),
            // $walletMostUnique->address,
            // trans('pages.statistics.insights.validators.header.least_unique_voters'),
            // $walletLeastUnique->address,
            trans('pages.statistics.insights.validators.header.oldest_active_validator'),
            $walletOldestActive->address,
            trans('pages.statistics.insights.validators.header.newest_active_validator'),
            $walletNewestActive->address,
            trans('pages.statistics.insights.validators.header.most_blocks_forged'),
            $walletMostBlocks->address,
        ])
        ->assertDontSee($randomWallet->address);
});

it('should render marketdata statistics for fiat', function (): void {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    Http::fake([
        'api.coingecko.com/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true), 200),
    ]);

    $this->app->singleton(NetworkContract::class, fn () => new Blockchain(config('arkscan.networks.production')));

    $crypto = app(CryptoDataCache::class);

    app(CacheVolume::class)->handle($crypto, new CoinGecko());

    $currentDate = Carbon::now();
    $currency    = 'USD';

    $cache = new StatisticsCache();
    $cache->setPriceRangeDaily($currency, 123, 456);
    $cache->setPriceRange52($currency, 12, 789);
    $cache->setPriceAtl($currency, $currentDate->subMonth()->timestamp, 0.4);
    $cache->setPriceAth($currency, $currentDate->timestamp, 987.3);

    $cache->setVolumeAtl($currency, $currentDate->subMonth()->timestamp, 10);
    $cache->setVolumeAth($currency, $currentDate->timestamp, 20000);

    $cache->setMarketCapAtl($currency, $currentDate->subMonth()->timestamp, 15);
    $cache->setMarketCapAth($currency, $currentDate->timestamp, 30000);

    (new NetworkStatusBlockCache())->setPrice('ARK', 'USD', 1.234);
    (new NetworkCache())->setSupply(fn () => 4.567 * 1e8);

    Livewire::test(Insights::class)
        ->assertSeeInOrder([
            trans('pages.statistics.insights.market_data.header.daily'),
            '123.00', '456.00',
            trans('pages.statistics.insights.market_data.header.year'),
            '12.00', '789.00',
            trans('pages.statistics.insights.market_data.header.atl'),
            '0.40',
            trans('pages.statistics.insights.market_data.header.ath'),
            '987.30',

            '16,232,625',
            trans('pages.statistics.insights.market_data.header.atl'),
            '10',
            trans('pages.statistics.insights.market_data.header.ath'),
            '20,000',

            '6',
            trans('pages.statistics.insights.market_data.header.atl'),
            '15',
            trans('pages.statistics.insights.market_data.header.ath'),
            '30,000',
        ]);
});

it('should render marketdata statistics for crypto', function (): void {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    Http::fake([
        'api.coingecko.com/*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/coingecko/coin.json')), true), 200),
    ]);

    Settings::shouldReceive('currency')
        ->andReturn('BTC');

    $this->app->singleton(NetworkContract::class, fn () => new Blockchain(config('arkscan.networks.production')));

    $crypto = app(CryptoDataCache::class);

    app(CacheVolume::class)->handle($crypto, new CoinGecko());

    $currentDate = Carbon::now();
    $currency    = 'BTC';

    $cache = new StatisticsCache();
    $cache->setPriceRangeDaily($currency, 0.00000123, 0.00000456);
    $cache->setPriceRange52($currency, 0.00000012, 0.00000789);
    $cache->setPriceAtl($currency, $currentDate->subMonth()->timestamp, 0.0000004);
    $cache->setPriceAth($currency, $currentDate->timestamp, 0.00009873);

    $cache->setVolumeAtl($currency, $currentDate->subMonth()->timestamp, 0.000001);
    $cache->setVolumeAth($currency, $currentDate->timestamp, 0.0002);

    $cache->setMarketCapAtl($currency, $currentDate->subMonth()->timestamp, 0.00000015);
    $cache->setMarketCapAth($currency, $currentDate->timestamp, 0.0003);

    (new NetworkStatusBlockCache())->setPrice('ARK', 'BTC', 0.00001234);
    (new NetworkCache())->setSupply(fn () => 4.567 * 1e8);

    Livewire::test(Insights::class)
        ->assertSeeInOrder([
            trans('pages.statistics.insights.market_data.header.daily'),
            '0.00000123 BTC', '0.00000456 BTC',
            trans('pages.statistics.insights.market_data.header.year'),
            '0.00000012 BTC', '0.00000789 BTC',
            trans('pages.statistics.insights.market_data.header.atl'),
            '0.0000004 BTC',
            trans('pages.statistics.insights.market_data.header.ath'),
            '0.00009873 BTC',

            '355.786 BTC',
            trans('pages.statistics.insights.market_data.header.atl'),
            '0.000001 BTC',
            trans('pages.statistics.insights.market_data.header.ath'),
            '0.0002 BTC',

            '0.00005636 BTC',
            trans('pages.statistics.insights.market_data.header.atl'),
            '0.00000015 BTC',
            trans('pages.statistics.insights.market_data.header.ath'),
            '0.0003 BTC',
        ]);
});

it('should render annual statistics', function (): void {
    $cache = new StatisticsCache();
    $cache->setAnnualData(2020, 12, '1234', '456', 28);
    $cache->setAnnualData(2021, 34, '12345', '4567', 39);

    Livewire::test(Insights::class)
        ->assertSeeInOrder([
            2020,
            trans('pages.statistics.insights.annual.header.transaction'),
            12,
            trans('pages.statistics.insights.annual.header.volume'),
            '1,234.00',
            trans('pages.statistics.insights.annual.header.fees'),
            '456.00',
            trans('pages.statistics.insights.annual.header.blocks'),
            28,

            2021,
            trans('pages.statistics.insights.annual.header.transaction'),
            34,
            trans('pages.statistics.insights.annual.header.volume'),
            '12,345.00',
            trans('pages.statistics.insights.annual.header.fees'),
            '4,567.00',
            trans('pages.statistics.insights.annual.header.blocks'),
            39,
        ]);
});
