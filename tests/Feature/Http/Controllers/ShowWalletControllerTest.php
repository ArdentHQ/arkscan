<?php

declare(strict_types=1);

use App\Facades\Settings;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\DelegateCache;
use App\Services\Cache\NetworkCache;
use App\ViewModels\ViewModelFactory;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Compilers\BladeCompiler;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $wallet = Wallet::factory()->create();

    (new NetworkCache())->setSupply(fn () => '10000000000');

    ((new DelegateCache())->setTotalAmounts(fn () => [$wallet->public_key => '1000000000']));
    ((new DelegateCache())->setTotalFees(fn () => [$wallet->public_key => '1000000000']));
    ((new DelegateCache())->setTotalRewards(fn () => [$wallet->public_key => '1000000000']));
    ((new DelegateCache())->setTotalBlocks(fn () => [$wallet->public_key => '1000000000']));

    $this
        ->get(route('wallet', $wallet))
        ->assertOk();
});

it('can lookup wallets by the username', function () {
    $this->withoutExceptionHandling();

    $wallet   = Wallet::factory()->create();
    $username = $wallet->attributes['delegate']['username'];

    (new NetworkCache())->setSupply(fn () => '10000000000');

    ((new DelegateCache())->setTotalAmounts(fn () => [$wallet->public_key => '1000000000']));
    ((new DelegateCache())->setTotalFees(fn () => [$wallet->public_key => '1000000000']));
    ((new DelegateCache())->setTotalRewards(fn () => [$wallet->public_key => '1000000000']));
    ((new DelegateCache())->setTotalBlocks(fn () => [$wallet->public_key => '1000000000']));

    expect($username)->not->toBeEmpty();

    $this
        ->get('/wallets/'.$username)
        ->assertRedirect('/addresses/'.$wallet->address);
});

it('should not double up currency for crypto', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    Settings::shouldReceive('currency')
        ->andReturn('BTC')
        ->shouldReceive('get')
        ->andReturnNull();

    $wallet = Wallet::factory()->create();

    $response = $this
        ->get(route('wallet', $wallet))
        ->assertSee($wallet->username)
        ->assertSee('0 BTC');

    $content = preg_replace('/\s+/', ' ', str_replace("\n", '', strip_tags($response->getContent())));

    expect($content)->not->toContain('0 BTC BTC Voting For');
});

it('should show currency symbol and code for crypto', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    Settings::shouldReceive('currency')
        ->andReturn('GBP')
        ->shouldReceive('get')
        ->andReturnNull();

    $wallet = Wallet::factory()->create();

    $response = $this
        ->get(route('wallet', $wallet))
        ->assertSee($wallet->username);

    $content = preg_replace('/\s+/', ' ', str_replace("\n", '', strip_tags($response->getContent())));

    expect($content)->toContain('£0.00 GBP Voting For');
});

it('should not show overview value if cannot be exchanged', function () {
    Config::set('arkscan.networks.development.canBeExchanged', false);

    Settings::shouldReceive('currency')
        ->andReturn('GBP')
        ->shouldReceive('get')
        ->andReturnNull();

    $wallet = Wallet::factory()->create();

    $this
        ->get(route('wallet', $wallet))
        ->assertSee($wallet->username)
        ->assertSeeInOrder([
            'Value',
            'N/A',
            'Voting For',
        ]);
});

it('should filter transactions in url', function () {
    $wallet = Wallet::factory()->create();

    $transaction = Transaction::factory()->transfer()->create([
        'sender_public_key' => $wallet->public_key,
    ]);

    Route::get('/test-transactions/{wallet}', function () use ($wallet) {
        return BladeCompiler::render('<livewire:wallet-transaction-table :wallet="$wallet" :defer-loading="false" />', ['wallet' => ViewModelFactory::make($wallet)]);
    });

    $this
        ->get('/test-transactions/'.$wallet->address)
        ->assertSee($transaction->id);

    $this
        ->get('/test-transactions/'.$wallet->address.'?outgoing=false')
        ->assertDontSee($transaction->id);

    $this
        ->get('/test-transactions/'.$wallet->address.'?outgoing=0')
        ->assertDontSee($transaction->id);

    $this
        ->get('/test-transactions/'.$wallet->address.'?outgoing=1')
        ->assertSee($transaction->id);
});
