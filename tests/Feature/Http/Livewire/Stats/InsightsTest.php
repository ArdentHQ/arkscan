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
use App\Models\MultiPayment;
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
use App\Services\Transactions\Aggregates\Historical\AveragesAggregate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use function Tests\faker;
use Tests\Feature\Http\Livewire\__stubs\NetworkStub;

it('should render transaction details', function (): void {
    $wallet = Wallet::factory()->activeValidator()->create();

    Transaction::factory(12)->validatorRegistration()->create();
    Transaction::factory(13)->validatorResignation()->create();
    Transaction::factory(24)->validatorUpdate()->create();
    Transaction::factory(14)->transfer()->create(['value' => 1 * 1e18]);
    Transaction::factory(15)->vote($wallet['address'])->create();
    Transaction::factory(16)->unvote()->create();
    Transaction::factory(19)->usernameRegistration('test')->create();
    Transaction::factory(20)->usernameResignation()->create();

    $largest = Transaction::factory()
        ->multiPayment([faker()->wallet['address']], [BigNumber::new(1 * 1e18)])
        ->create([
            'value'     => 9999 * 1e18,
            'gas_price' => 11 * 1e18,
        ]);

    Transaction::factory(17)
        ->multiPayment([faker()->wallet['address']], [BigNumber::new(1 * 1e18)])
        ->create([
            'value'     => 2 * 1e18,
            'gas_price' => 11 * 1e18,
            'gas_used'  => 1e9,
        ]);

    $transactionCache = new TransactionCache();
    $transactionCache->getCache()->flush();

    Artisan::call('explorer:cache-transactions');

    $largestTransaction = $largest->fresh();

    $transactionDetails = TransactionStatistics::make(
        [
            'transfer'               => 14,
            'multipayment'           => 18,
            'vote'                   => 15,
            'unvote'                 => 16,
            'validator_registration' => 12,
            'validator_resignation'  => 13,
            'validator_update'       => 24,
            'username_registration'  => 19,
            'username_resignation'   => 20,
        ],
        TransactionAveragesStatistics::make($transactionCache->getHistoricalAverages()),
        TransactionRecordsStatistics::make($largestTransaction),
    );

    $component = Livewire::test(Insights::class)
        ->assertSeeInOrder([
            trans('pages.statistics.insights.transactions.header.transfer'),
            '14',
            trans('pages.statistics.insights.transactions.header.multipayment'),
            '18',
            trans('pages.statistics.insights.transactions.header.vote'),
            '15',
            trans('pages.statistics.insights.transactions.header.unvote'),
            '16',
            trans('pages.statistics.insights.transactions.header.validator_registration'),
            '12',
            trans('pages.statistics.insights.transactions.header.validator_resignation'),
            '13',
            trans('pages.statistics.insights.transactions.header.validator_update'),
            '24',
            trans('pages.statistics.insights.transactions.header.username_registration'),
            '19',
            trans('pages.statistics.insights.transactions.header.username_resignation'),
            '20',
        ]);

    $actualTransactionDetails = $component->viewData('transactionDetails');

    expect($actualTransactionDetails->details)->toEqual($transactionDetails->details);
    expect($actualTransactionDetails->averages)->toEqual($transactionDetails->averages);
    expect($actualTransactionDetails->records->largestTransaction->hash())->toEqual($transactionDetails->records->largestTransaction->hash());
});

it('should render transaction daily average', function (): void {
    $daysSinceEpoch = 2;
    $networkStub    = new NetworkStub(true, Carbon::now()->subDay($daysSinceEpoch));
    app()->singleton(NetworkContract::class, fn () => $networkStub);

    $transactionCache = new TransactionCache();

    Transaction::factory(2)
        ->validatorRegistration()
        ->create([
            'value'     => 0,
            'gas_price' => 9,
            'status'    => true,
        ]);

    Transaction::factory(3)
        ->transfer()
        ->create([
            'value'     => 2000 * 1e18,
            'gas_price' => 10,
            'status'    => true,
        ]);

    $recipientAddress = faker()->wallet['address'];
    $amount           = BigNumber::new(1000 * 1e18);
    Transaction::factory(4)
        ->multiPayment([$recipientAddress], [$amount])
        ->create([
            'gas_price' => 11,
            'status'    => true,
        ])->each(function ($transaction) use ($recipientAddress, $amount) {
            MultiPayment::factory()
                ->create([
                    'to'     => $recipientAddress,
                    'from'   => $transaction->from,
                    'hash'   => $transaction->hash,
                    'amount' => $amount,
                ]);
        });

    expect(Transaction::count())->toBe(9);

    $transactionCount = (int) round(9 / $daysSinceEpoch);
    $totalAmount      = (int) round(((1000 * 4) + (3 * 2000)) / $daysSinceEpoch);
    $totalFees        = (float) round((((9 * 2) + (10 * 3) + (11 * 4)) * 21000) / $daysSinceEpoch);

    expect((new AveragesAggregate())->aggregate())->toBe([
        'count'  => $transactionCount,
        'amount' => $totalAmount,
        'fee'    => $totalFees,
    ]);

    Artisan::call('explorer:cache-transactions');

    $transactionDetails = TransactionStatistics::make(
        StatsTransactionType::all()
            ->mapWithKeys(fn ($type) => [$type => $transactionCache->getHistoricalByType($type)])
            ->toArray(),
        TransactionAveragesStatistics::make($transactionCache->getHistoricalAverages()),
        TransactionRecordsStatistics::make(Transaction::where('hash', $transactionCache->getLargestIdByAmount())->first()),
    );

    $component = Livewire::test(Insights::class)
        ->assertSeeInOrder([
            trans('pages.statistics.insights.transactions.header.transfer'),
            trans('pages.statistics.insights.transactions.header.multipayment'),
            trans('pages.statistics.insights.transactions.header.vote'),
            trans('pages.statistics.insights.transactions.header.unvote'),
            trans('pages.statistics.insights.transactions.header.validator_registration'),
            trans('pages.statistics.insights.transactions.header.validator_resignation'),
            trans('pages.statistics.insights.transactions.header.validator_update'),
            trans('pages.statistics.insights.transactions.header.username_registration'),
            trans('pages.statistics.insights.transactions.header.username_resignation'),
            trans('pages.statistics.insights.transactions.header.transactions'),
            $transactionCount,
            trans('pages.statistics.insights.transactions.header.transaction_volume'),
            number_format($totalAmount).' DARK',
            trans('pages.statistics.insights.transactions.header.transaction_fees'),
            number_format($totalFees).' DARK',
        ]);

    $actualTransactionDetails = $component->viewData('transactionDetails');

    expect($actualTransactionDetails->details)->toEqual($transactionDetails->details);
    expect($actualTransactionDetails->averages)->toEqual($transactionDetails->averages);
    expect($actualTransactionDetails->records->largestTransaction->hash())->toEqual($transactionDetails->records->largestTransaction->hash());
});

it('should render transaction records', function (): void {
    $largestTransaction        = Transaction::factory()->transfer()->create();
    $otherTransaction          = Transaction::factory()->transfer()->create();
    $largestBlock              = Block::factory()->create();
    $largestBlockFee           = Block::factory()->create();
    $blockWithMostTransactions = Block::factory()->create();
    $otherBlock                = Block::factory()->create();

    (new TransactionCache())->setLargestIdByAmount($largestTransaction->hash);
    (new BlockCache())->setLargestIdByFees($largestBlockFee->hash);
    (new BlockCache())->setLargestIdByTransactionCount($blockWithMostTransactions->hash);

    Livewire::test(Insights::class)
        ->assertSeeInOrder([
            trans('pages.statistics.insights.transactions.header.largest_transaction'),
            $largestTransaction->hash,
            trans('pages.statistics.insights.transactions.header.highest_fee'),
            $largestBlockFee->hash,
            trans('pages.statistics.insights.transactions.header.most_transactions_in_block'),
            $blockWithMostTransactions->hash,
        ])
        ->assertDontSee($otherTransaction->hash)
        ->assertDontSee($otherBlock->hash);
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
    $cache->setMostUniqueVoters($walletMostUnique->address);
    $cache->setLeastUniqueVoters($walletLeastUnique->address);
    $cache->setOldestActiveValidator($walletOldestActive->address, $currentDate->subMonth()->timestamp);
    $cache->setNewestActiveValidator($walletNewestActive->address, $currentDate->timestamp);
    $cache->setMostBlocksForged($walletMostBlocks->address);

    Livewire::test(Insights::class)
        ->assertSeeInOrder([
            trans('pages.statistics.insights.validators.header.most_unique_voters'),
            $walletMostUnique->address,
            trans('pages.statistics.insights.validators.header.least_unique_voters'),
            $walletLeastUnique->address,
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
    (new NetworkCache())->setSupply(fn () => 4.567 * 1e18);

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
    (new NetworkCache())->setSupply(fn () => 4.567 * 1e18);

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
    $cache->setAnnualData(2023, 12, '1234', '456', 28);
    $cache->setAnnualData(2024, 34, '12345', '4567', 39);

    Livewire::test(Insights::class)
        ->assertSeeInOrder([
            2023,
            trans('pages.statistics.insights.annual.header.transaction'),
            12,
            trans('pages.statistics.insights.annual.header.volume'),
            '1,234.00',
            trans('pages.statistics.insights.annual.header.fees'),
            '456.00',
            trans('pages.statistics.insights.annual.header.blocks'),
            28,

            2024,
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
