<?php

declare(strict_types=1);

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
