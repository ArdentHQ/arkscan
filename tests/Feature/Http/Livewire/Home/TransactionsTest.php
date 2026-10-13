<?php

declare(strict_types=1);

use App\Http\Livewire\Home\Transactions;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

it('should list the first page of transactions', function () {
    Transaction::factory(30)->transfer()->create([
        'amount' => 143.2232 * 1e8,
        'fee'    => 0.128373 * 1e8,
    ]);

    $component = Livewire::test(Transactions::class)
        ->call('setIsReady');

    foreach (ViewModelFactory::collection(Transaction::withScope(OrderByTimestampScope::class)->take(15)->get()) as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee('143.2232');
        $component->assertSee('0.128373');
    }
});

it('should cache the total transaction count', function () {
    Transaction::factory(5)->transfer()->create();

    expect(Cache::has('transactions_total_count'))->toBeFalse();

    Livewire::test(Transactions::class)->call('setIsReady');

    expect(Cache::get('transactions_total_count'))->toBe(5);
});

it('should use the cached total count instead of querying the database', function () {
    Transaction::factory(5)->transfer()->create();

    Cache::put('transactions_total_count', 999, 60);

    $component = Livewire::test(Transactions::class)->call('setIsReady');

    expect($component->get('transactions')->total())->toBe(999);
});

it('should show stale total count when new transactions are added after caching', function () {
    Transaction::factory(5)->transfer()->create();

    Livewire::test(Transactions::class)->call('setIsReady');

    expect(Cache::get('transactions_total_count'))->toBe(5);

    Transaction::factory(3)->transfer()->create();

    $component = Livewire::test(Transactions::class)->call('setIsReady');

    expect($component->get('transactions')->total())->toBe(5);
});

it('should reload on new transaction websocket event', function () {
    $component = Livewire::test(Transactions::class)
        ->call('setIsReady');

    $transaction = Transaction::factory()->transfer()->create();

    $component->assertDontSee($transaction->id)
        ->dispatch('echo:transactions,NewTransaction')
        ->assertSee($transaction->id);
});

it('should poll when broadcasting driver is not reverb', function () {
    Config::set('broadcasting.default', 'log');

    Livewire::test(Transactions::class)
        ->call('setIsReady')
        ->assertSee('wire:poll.10s', false);
});

it('should not poll when broadcasting driver is reverb', function () {
    Config::set('broadcasting.default', 'reverb');

    Livewire::test(Transactions::class)
        ->call('setIsReady')
        ->assertDontSee('wire:poll.10s', false);
});
