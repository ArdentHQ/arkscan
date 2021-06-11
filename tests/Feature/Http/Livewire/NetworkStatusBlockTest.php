<?php

declare(strict_types=1);

use App\Http\Livewire\NetworkStatusBlock;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\Settings;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use function Tests\configureExplorerDatabase;

it('should render with a height, supply and not available market cap', function () {
    configureExplorerDatabase();

    Block::factory()->create([
        'height'               => 5651290,
        'generator_public_key' => Wallet::factory()->create([
            'balance' => '13628098200000000',
        ])->public_key,
    ]);

    (new NetworkStatusBlockCache())->setPrice('USD', 'USD', 0.2907);

    Livewire::test(NetworkStatusBlock::class)
        ->assertSee('5,651,290')
        ->assertSee('136,280,982 DARK')
        ->assertSee('Not Available');
});

it('should render with a height, supply and market cap', function () {
    Config::set('explorer.network', 'production');

    configureExplorerDatabase();

    Block::factory()->create([
        'height'               => 5651290,
        'generator_public_key' => Wallet::factory()->create([
            'balance' => '13628098200000000',
        ])->public_key,
    ]);

    (new NetworkStatusBlockCache())->setPrice('ARK', 'USD', 1.606);
    (new NetworkStatusBlockCache())->setHistoricalHourly('ARK', 'USD', collect());

    Livewire::test(NetworkStatusBlock::class)
        ->assertSee('5,651,290') // Height
        ->assertSee('136,280,982 ARK') // Supply
        ->assertSee('$218,867,257.09') // Market cap
        ->assertSee('1.61'); // Price
});

it('should render with a height, supply and market cap for BTC', function () {
    Config::set('explorer.network', 'production');

    $settings = Settings::all();
    $settings['currency'] = 'BTC';

    Session::put('settings', json_encode($settings));

    configureExplorerDatabase();

    Block::factory()->create([
        'height'               => 5651290,
        'generator_public_key' => Wallet::factory()->create([
            'balance' => '13628098200000000',
        ])->public_key,
    ]);

    (new NetworkStatusBlockCache())->setPrice('ARK', 'BTC', 0.00003132);
    (new NetworkStatusBlockCache())->setHistoricalHourly('ARK', 'BTC', collect());

    Livewire::test(NetworkStatusBlock::class)
        ->assertSee('5,651,290') // Height
        ->assertSee('136,280,982 ARK') // Supply
        ->assertSee('4,268.32035624 BTC') // Market cap
        ->assertSee('0.00003132'); // Price
});

it('should render the price change', function () {
    Config::set('explorer.networks.development.canBeExchanged', true);

    (new NetworkStatusBlockCache())->setPriceChange('DARK', 'USD', 0.137);
    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 1);
    (new NetworkStatusBlockCache())->setHistoricalHourly('DARK', 'USD', collect());

    Livewire::test(NetworkStatusBlock::class)->assertSee('13.70%');
});

it('handle price change when price is zero', function () {
    Config::set('explorer.networks.development.canBeExchanged', true);

    (new NetworkStatusBlockCache())->setPriceChange('DARK', 'USD', 0);
    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 1);
    (new NetworkStatusBlockCache())->setHistoricalHourly('DARK', 'USD', collect());

    Livewire::test(NetworkStatusBlock::class)->assertSee('0.00%');
});
