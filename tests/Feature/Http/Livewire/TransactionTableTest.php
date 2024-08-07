<?php

declare(strict_types=1);

use App\Facades\Settings;
use App\Http\Livewire\TransactionTable;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\CryptoDataCache;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

it('should list the first page of records', function () {
    Transaction::factory(30)->transfer()->create([
        'amount' => 481 * 1e8,
        'fee'    => 0.481 * 1e8,
    ]);

    $component = Livewire::test(TransactionTable::class)
        ->call('setIsReady');

    foreach (ViewModelFactory::paginate(Transaction::withScope(OrderByTimestampScope::class)->paginate())->items() as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee('481.00');
        $component->assertSee('0.48');
    }
});

it('should update the records fiat tooltip when currency changed', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    (new CryptoDataCache())->setPrices('USD.week', collect([
        '2020-10-19' => 24210,
    ]));

    (new CryptoDataCache())->setPrices('BTC.week', collect([
        '2020-10-19' => 0.1234567,
    ]));

    Transaction::factory()->transfer()->create([
        'timestamp' => 112982056,
        'amount'    => 499 * 1e8,
    ]);

    $component = Livewire::test(TransactionTable::class)
        ->call('setIsReady');

    $expectedValue = NumberFormatter::currency(12080790, 'USD');

    $component->assertSeeHtml('data-tippy-content="'.$expectedValue.'"');
    $component->assertDontSeeHtml('data-tippy-content="61.6048933 BTC"');

    $settings             = Settings::all();
    $settings['currency'] = 'BTC';

    Settings::shouldReceive('all')->andReturn($settings);
    Settings::shouldReceive('currency')->andReturn('BTC');

    $component->emit('currencyChanged', 'BTC');

    $component->assertDontSeeHtml('data-tippy-content="'.$expectedValue.'"');
    $component->assertSeeHtml('data-tippy-content="61.6048933 BTC"');
});

it('should toggle all filters when "select all" is selected', function () {
    Livewire::test(TransactionTable::class)
        ->call('setIsReady')
        ->assertSet('filter', [
            'transfers'     => true,
            'votes'         => true,
            'multipayments' => true,
            'others'        => true,
        ])
        ->assertSet('selectAllFilters', true)
        ->set('filter.transfers', true)
        ->assertSet('selectAllFilters', true)
        ->set('selectAllFilters', false)
        ->assertSet('filter', [
            'transfers'     => false,
            'votes'         => false,
            'multipayments' => false,
            'others'        => false,
        ])
        ->set('selectAllFilters', true)
        ->assertSet('filter', [
            'transfers'     => true,
            'votes'         => true,
            'multipayments' => true,
            'others'        => true,
        ]);
});

it('should toggle "select all" when all filters are selected', function () {
    Livewire::test(TransactionTable::class)
        ->call('setIsReady')
        ->assertSet('filter', [
            'transfers'     => true,
            'votes'         => true,
            'multipayments' => true,
            'others'        => true,
        ])
        ->assertSet('selectAllFilters', true)
        ->set('filter.transfers', false)
        ->assertSet('selectAllFilters', false)
        ->set('filter.transfers', true)
        ->assertSet('selectAllFilters', true);
});

it('should filter by transfer transactions', function () {
    $transfer = Transaction::factory()->transfer()->create();

    $wallet = Wallet::factory()->activeDelegate()->create();
    $vote   = Transaction::factory()->vote()->create([
        'sender_public_key' => $wallet->public_key,
        'asset'             => [
            'votes' => ['+'.$wallet->public_key],
        ],
    ]);

    Livewire::test(TransactionTable::class)
        ->call('setIsReady')
        ->set('filter', [
            'transfers'     => true,
            'votes'         => false,
            'multipayments' => false,
            'others'        => false,
        ])
        ->assertSee($transfer->id)
        ->assertDontSee($vote->id);
});

it('should filter by vote transactions', function () {
    $transfer = Transaction::factory()->transfer()->create();

    $wallet = Wallet::factory()->activeDelegate()->create();
    $vote   = Transaction::factory()->vote()->create([
        'sender_public_key' => $wallet->public_key,
        'asset'             => [
            'votes' => ['+'.$wallet->public_key],
        ],
    ]);

    Livewire::test(TransactionTable::class)
        ->call('setIsReady')
        ->set('filter', [
            'transfers'     => false,
            'votes'         => true,
            'multipayments' => false,
            'others'        => false,
        ])
        ->assertSee($vote->id)
        ->assertDontSee($transfer->id);
});

it('should filter by multipayment transactions', function () {
    $transfer = Transaction::factory()->transfer()->create();

    $multipayment = Transaction::factory()->multiPayment()->create([
        'asset' => [
            'payments' => [],
        ],
    ]);

    Livewire::test(TransactionTable::class)
        ->call('setIsReady')
        ->set('filter', [
            'transfers'     => false,
            'votes'         => false,
            'multipayments' => true,
            'others'        => false,
        ])
        ->assertSee($multipayment->id)
        ->assertDontSee($transfer->id);
});

it('should filter by other transactions', function () {
    $transfer = Transaction::factory()->transfer()->create();

    $delegateRegistration = Transaction::factory()->delegateRegistration()->create();

    $entityRegistration = Transaction::factory()->entityRegistration()->create();

    Livewire::test(TransactionTable::class)
        ->call('setIsReady')
        ->set('filter', [
            'transfers'     => false,
            'votes'         => false,
            'multipayments' => false,
            'others'        => true,
        ])
        ->assertSee($delegateRegistration->id)
        ->assertSee($entityRegistration->id)
        ->assertDontSee($transfer->id);
});

it('should show no transactions if no type filter', function () {
    $transfer = Transaction::factory()->transfer()->create();

    $delegateRegistration = Transaction::factory()->delegateRegistration()->create();

    $entityRegistration = Transaction::factory()->entityRegistration()->create();

    Livewire::test(TransactionTable::class)
        ->call('setIsReady')
        ->set('filter', [
            'transfers'     => false,
            'votes'         => false,
            'multipayments' => false,
            'others'        => false,
        ])
        ->assertDontSee($transfer->id)
        ->assertDontSee($delegateRegistration->id)
        ->assertDontSee($entityRegistration->id)
        ->assertSee(trans('tables.transactions.no_results.no_filters'));
});

it('should reload on new transaction event', function () {
    $component = Livewire::test(TransactionTable::class)
        ->call('setIsReady');

    Transaction::factory(5)->transfer()->create([
        'amount' => 481 * 1e8,
        'fee'    => 0.481 * 1e8,
    ]);

    foreach (ViewModelFactory::paginate(Transaction::withScope(OrderByTimestampScope::class)->paginate())->items() as $transaction) {
        $component->assertDontSee($transaction->id());
        $component->assertDontSee($transaction->timestamp());
        $component->assertDontSee($transaction->sender()->address());
        $component->assertDontSee($transaction->recipient()->address());
        $component->assertDontSee('481.00');
        $component->assertDontSee('0.48');
    }

    $component->emit('echo:transactions,NewTransaction');

    foreach (ViewModelFactory::paginate(Transaction::withScope(OrderByTimestampScope::class)->paginate())->items() as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee('481.00');
        $component->assertSee('0.48');
    }
});
