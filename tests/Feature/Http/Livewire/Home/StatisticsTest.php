<?php

declare(strict_types=1);

use App\Facades\Settings;
use App\Http\Livewire\Home\Statistics;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

it('should render with a height, volume, supply and not available market cap', function () {
    Config::set('arkscan.networks.development.canBeExchanged', false);

    Block::factory()->create([
        'height'               => 5651290,
        'generator_public_key' => Wallet::factory()->create([
            'balance' => '13628098200000000',
        ])->public_key,
    ]);

    Livewire::test(Statistics::class)
        ->assertSeeInOrder([
            'N/A', // Market Cap
            '136,280,982 DARK', // Supply
            'N/A', // Volume
            '5,651,290', // Height
        ]);
});

it('should render with a height, volume, supply and market cap', function () {
    Config::set('arkscan.network', 'production');

    Block::factory()->create([
        'height'               => 5651290,
        'generator_public_key' => Wallet::factory()->create([
            'balance' => '13628098200000000',
        ])->public_key,
    ]);

    $transaction = Transaction::factory()->transfer()->create([
        'timestamp' => Timestamp::fromUnix(Carbon::now()->unix())->unix(),
        'amount'    => 18204 * 1e8,
        'fee'       => 0.99 * 1e8,
    ]);

    $transaction->sender->balance          = 0;
    $transaction->block->delegate->balance = 0;
    $transaction->sender->save();
    $transaction->block->delegate->save();

    (new NetworkStatusBlockCache())->setPrice('ARK', 'USD', 1.606);
    (new NetworkStatusBlockCache())->setHistoricalHourly('ARK', 'USD', collect());
    (new CryptoDataCache())->setVolume('USD', '12345');

    Livewire::test(Statistics::class)
        ->assertSee('5,651,290') // Height
        ->assertSee('12,345') // Volume
        ->assertSee('136,280,982 ARK') // Supply
        ->assertSee('$218,867,257'); // Market cap
});

it('should render with a height, volume, supply and market cap for BTC', function () {
    Config::set('arkscan.network', 'production');

    $settings             = Settings::all();
    $settings['currency'] = 'BTC';

    Settings::shouldReceive('all')->andReturn($settings);
    Settings::shouldReceive('currency')->andReturn('BTC');
    Settings::shouldReceive('usesDarkTheme')->andReturn(false);

    Block::factory()->create([
        'height'               => 5651290,
        'generator_public_key' => Wallet::factory()->create([
            'balance' => '13628098200000000',
        ])->public_key,
    ]);

    $transaction = Transaction::factory()->transfer()->create([
        'timestamp' => Timestamp::fromUnix(Carbon::now()->unix())->unix(),
        'amount'    => 18204 * 1e8,
        'fee'       => 0.99 * 1e8,
    ]);

    $transaction->sender->balance          = 0;
    $transaction->block->delegate->balance = 0;
    $transaction->sender->save();
    $transaction->block->delegate->save();

    (new NetworkStatusBlockCache())->setPrice('ARK', 'BTC', 0.00003132);
    (new NetworkStatusBlockCache())->setHistoricalHourly('ARK', 'BTC', collect());
    (new CryptoDataCache())->setVolume('BTC', '123');

    Livewire::test(Statistics::class)
        ->assertSee('5,651,290') // Height
        ->assertSee('123') // Volume
        ->assertSee('136,280,982 ARK') // Supply
        ->assertSee('4,268.32035624 BTC'); // Market cap
});
