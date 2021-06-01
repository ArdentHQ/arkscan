<?php

declare(strict_types=1);

use App\Http\Livewire\NetworkStatusBlock;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\Cache\CryptoCompareCache;
use App\Services\Settings;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use function Tests\configureExplorerDatabase;

it('should render with a height, supply and not available market cap', function () {
    configureExplorerDatabase();

    Http::fake([
        'cryptocompare.com/data/pricemultifull*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/pricemultifull.json')), true), 200),
        'cryptocompare.com/data/price*'          => Http::response(['USD' => 0.2907], 200),
        'cryptocompare.com/data/histoday*'       => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/historical.json')), true), 200),
        'cryptocompare.com/data/histohour*'      => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histohour.json')), true), 200),
    ]);

    Block::factory()->create([
        'height'               => 5651290,
        'generator_public_key' => Wallet::factory()->create([
            'balance' => '13628098200000000',
        ])->public_key,
    ]);

    (new CryptoCompareCache())->setPrice('USD', 'USD', fn () => 0.2907);

    Livewire::test(NetworkStatusBlock::class)
        ->assertSee('5,651,290')
        ->assertSee('136,280,982 DARK')
        ->assertSee('Not Available');
});

it('should render with a height, supply and market cap', function () {
    Config::set('explorer.network', 'production');

    Http::fake([
        'cryptocompare.com/data/pricemultifull*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/pricemultifull.json')), true), 200),
        'cryptocompare.com/data/price*'          => Http::response(['USD' => 0.2907], 200),
        'cryptocompare.com/data/histoday*'       => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/historical.json')), true), 200),
        'cryptocompare.com/data/histohour*'      => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histohour.json')), true), 200),
    ]);

    configureExplorerDatabase();

    Block::factory()->create([
        'height'               => 5651290,
        'generator_public_key' => Wallet::factory()->create([
            'balance' => '13628098200000000',
        ])->public_key,
    ]);

    (new CryptoCompareCache())->setPrice('USD', 'USD', fn () => 1.646);

    Livewire::test(NetworkStatusBlock::class)
        ->assertSee('5,651,290') // Height
        ->assertSee('136,280,982 ARK') // Supply
        ->assertSee('254,260,570.60') // Market cap
        ->assertSee('1.61'); // Price
});

it('should render with a height, supply and market cap for BTC', function () {
    Config::set('explorer.network', 'production');

    $settings = Settings::all();
    $settings['currency'] = 'BTC';

    Session::put('settings', json_encode($settings));

    Http::fake([
        'cryptocompare.com/data/pricemultifull*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/pricemultifull.json')), true), 200),
        'cryptocompare.com/data/price*'          => Http::response(['BTC' => 0.00003132], 200),
        'cryptocompare.com/data/histoday*'       => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/historical.json')), true), 200),
        'cryptocompare.com/data/histohour*'      => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histohour.json')), true), 200),
    ]);

    configureExplorerDatabase();

    Block::factory()->create([
        'height'               => 5651290,
        'generator_public_key' => Wallet::factory()->create([
            'balance' => '13628098200000000',
        ])->public_key,
    ]);

    (new CryptoCompareCache())->setPrice('BTC', 'BTC', fn () => 0.00003132);

    Livewire::test(NetworkStatusBlock::class)
        ->assertSee('5,651,290') // Height
        ->assertSee('136,280,982 ARK') // Supply
        ->assertSee('4,934.2677444') // Market cap
        ->assertSee('0.00003132'); // Price
});

it('should render the price change', function () {
    Config::set('explorer.networks.development.canBeExchanged', true);

    Http::fake([
        'cryptocompare.com/data/histohour*' => Http::response(json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histohour.json')), true)),
    ]);

    Livewire::test(NetworkStatusBlock::class)->assertSee('13.70%');
});

it('handle price change when price is zero', function () {
    Config::set('explorer.networks.development.canBeExchanged', true);

    $response = json_decode(file_get_contents(base_path('tests/fixtures/cryptocompare/histohour.json')), true);
    // Force 0 price
    $response['Data'] = collect($response['Data'])->map(fn ($item) => array_merge($item, ['close' => 0]))->toArray();

    Http::fake([
        'cryptocompare.com/data/histohour*' => Http::response($response),
    ]);

    Livewire::test(NetworkStatusBlock::class)->assertSee('0.00%');
});
