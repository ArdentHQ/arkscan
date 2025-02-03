<?php

declare(strict_types=1);

use App\Facades\Settings;
use App\Http\Livewire\TransactionTable;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\CryptoDataCache;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use function Tests\faker;

it('should list the first page of records', function () {
    Transaction::factory(30)->transfer()->create([
        'amount' => 481 * 1e18,
    ]);

    $component = Livewire::test(TransactionTable::class)
        ->call('setIsReady');

    foreach (ViewModelFactory::paginate(Transaction::withScope(OrderByTimestampScope::class)->paginate())->items() as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee('481.00');
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
        'timestamp' => Carbon::parse('2020-10-19 05:54:16')->getTimestampMs(),
        'amount'    => 499 * 1e18,
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

    $component->dispatch('currencyChanged', 'BTC');

    $component->assertDontSeeHtml('data-tippy-content="'.$expectedValue.'"');
    $component->assertSeeHtml('data-tippy-content="61.6048933 BTC"');
});

it('should toggle all filters when "select all" is selected', function () {
    Livewire::test(TransactionTable::class)
        ->call('setIsReady')
        ->assertSet('filter', [
            'transfers'           => true,
            'multipayments'       => true,
            'votes'               => true,
            'validator'           => true,
            'username'            => true,
            'contract_deployment' => true,
            'others'              => true,
        ])
        ->assertSet('selectAllFilters', true)
        ->set('filter.transfers', true)
        ->assertSet('selectAllFilters', true)
        ->set('selectAllFilters', false)
        ->assertSet('filter', [
            'transfers'           => false,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => false,
        ])
        ->set('selectAllFilters', true)
        ->assertSet('filter', [
            'transfers'           => true,
            'multipayments'       => true,
            'votes'               => true,
            'validator'           => true,
            'username'            => true,
            'contract_deployment' => true,
            'others'              => true,
        ]);
});

it('should toggle "select all" when all filters are selected', function () {
    Livewire::test(TransactionTable::class)
        ->call('setIsReady')
        ->assertSet('filter', [
            'transfers'           => true,
            'multipayments'       => true,
            'votes'               => true,
            'validator'           => true,
            'username'            => true,
            'contract_deployment' => true,
            'others'              => true,
        ])
        ->assertSet('selectAllFilters', true)
        ->set('filter.transfers', false)
        ->assertSet('selectAllFilters', false)
        ->set('filter.transfers', true)
        ->assertSet('selectAllFilters', true);
});

it('should filter by transfer transactions', function () {
    $transfer = Transaction::factory()->transfer()->create();

    $wallet = Wallet::factory()->activeValidator()->create();
    $vote   = Transaction::factory()->vote($wallet->address)->create([
        'sender_public_key' => $wallet->public_key,
    ]);

    Livewire::test(TransactionTable::class)
        ->call('setIsReady')
        ->set('filter', [
            'transfers'           => true,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => false,
        ])
        ->assertSee($transfer->id)
        ->assertDontSee($vote->id);
});

it('should filter by multipayment transactions', function () {
    $transfer = Transaction::factory()->transfer()->create();

    $multipayment = Transaction::factory()
        ->multiPayment([faker()->wallet['address']], [BigNumber::new(1 * 1e18)])
        ->create();

    Livewire::test(TransactionTable::class)
        ->call('setIsReady')
        ->set('filter', [
            'transfers'           => false,
            'multipayments'       => true,
            'votes'               => false,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => false,
        ])
        ->assertSee($multipayment->id)
        ->assertDontSee($transfer->id);
});

it('should filter by vote transactions', function () {
    $transfer = Transaction::factory()->transfer()->create();

    $wallet = Wallet::factory()->activeValidator()->create();

    $vote = Transaction::factory()->vote($wallet->address)->create([
        'sender_public_key' => $wallet->public_key,
    ]);

    $unvote = Transaction::factory()->unvote()->create([
        'sender_public_key' => $wallet->public_key,
    ]);

    Livewire::test(TransactionTable::class)
        ->call('setIsReady')
        ->set('filter', [
            'transfers'           => false,
            'multipayments'       => false,
            'votes'               => true,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => false,
        ])
        ->assertSee($vote->id)
        ->assertSee($unvote->id)
        ->assertDontSee($transfer->id);
});

it('should filter by validator transactions', function () {
    $transfer = Transaction::factory()->transfer()->create();

    $wallet = Wallet::factory()->activeValidator()->create();

    $registration = Transaction::factory()->validatorRegistration()->create([
        'sender_public_key' => $wallet->public_key,
    ]);

    $resignation = Transaction::factory()->validatorResignation()->create([
        'sender_public_key' => $wallet->public_key,
    ]);

    Livewire::test(TransactionTable::class)
        ->call('setIsReady')
        ->set('filter', [
            'transfers'           => false,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => true,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => false,
        ])
        ->assertSee($registration->id)
        ->assertSee($resignation->id)
        ->assertDontSee($transfer->id);
});

it('should filter by username transactions', function () {
    $transfer = Transaction::factory()->transfer()->create();

    $registration = Transaction::factory()->usernameRegistration()->create();

    $resignation = Transaction::factory()->usernameResignation()->create();

    Livewire::test(TransactionTable::class)
        ->call('setIsReady')
        ->set('filter', [
            'transfers'           => false,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => false,
            'username'            => true,
            'contract_deployment' => false,
            'others'              => false,
        ])
        ->assertSee($registration->id)
        ->assertSee($resignation->id)
        ->assertDontSee($transfer->id);
});

it('should filter by contract deployment transactions', function () {
    $transfer = Transaction::factory()->transfer()->create();

    $wallet             = Wallet::factory()->activeValidator()->create();
    $contractDeployment = Transaction::factory()->contractDeployment()->create([
        'sender_public_key' => $wallet->public_key,
    ]);

    Livewire::test(TransactionTable::class)
        ->call('setIsReady')
        ->set('filter', [
            'transfers'           => false,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => true,
            'others'              => false,
        ])
        ->assertSee($contractDeployment->id)
        ->assertDontSee($transfer->id);
});

it('should filter by other transactions', function () {
    $transfer = Transaction::factory()->transfer()->create();

    $other = Transaction::factory()->withPayload('12345678')->create();

    Livewire::test(TransactionTable::class)
        ->call('setIsReady')
        ->set('filter', [
            'transfers'           => false,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => true,
        ])
        ->assertSee($other->id)
        ->assertDontSee($transfer->id);
});

it('should show no transactions if no type filter', function () {
    $transfer = Transaction::factory()->transfer()->create();

    $validatorRegistration = Transaction::factory()->validatorRegistration()->create();

    Livewire::test(TransactionTable::class)
        ->call('setIsReady')
        ->set('filter', [
            'transfers'           => false,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => false,
        ])
        ->assertDontSee($transfer->id)
        ->assertDontSee($validatorRegistration->id)
        ->assertSee(trans('tables.transactions.no_results.no_filters'));
});

it('should get the filter values via a getter', function () {
    $instance = Livewire::test(TransactionTable::class)
        ->call('setIsReady')
        ->set('filter', [
            'transfers'           => false,
            'multipayments'       => false,
            'votes'               => true,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => true,
        ])
        ->instance();

    expect($instance->transfers)->toBeFalse();
    expect($instance->multipayments)->toBeFalse();
    expect($instance->votes)->toBeTrue();
    expect($instance->validator)->toBeFalse();
    expect($instance->username)->toBeFalse();
    expect($instance->contract_deployment)->toBeFalse();
    expect($instance->others)->toBeTrue();
});

it('should set the filter values via a setter', function () {
    $instance = Livewire::test(TransactionTable::class)
        ->call('setIsReady')
        ->set('filter', [
            'transfers'           => false,
            'multipayments'       => false,
            'votes'               => false,
            'validator'           => false,
            'username'            => false,
            'contract_deployment' => false,
            'others'              => false,
        ])
        ->instance();

    $instance->transfers           = true;
    $instance->multipayments       = true;
    $instance->votes               = true;
    $instance->validator           = true;
    $instance->username            = true;
    $instance->contract_deployment = true;
    $instance->others              = true;

    expect($instance->transfers)->toBeTrue();
    expect($instance->multipayments)->toBeTrue();
    expect($instance->votes)->toBeTrue();
    expect($instance->validator)->toBeTrue();
    expect($instance->username)->toBeTrue();
    expect($instance->contract_deployment)->toBeTrue();
    expect($instance->others)->toBeTrue();
});

it('should reload on new transaction event', function () {
    $component = Livewire::test(TransactionTable::class)
        ->call('setIsReady');

    Transaction::factory(5)->transfer()->create([
        'amount' => 481 * 1e18,
    ]);

    foreach (ViewModelFactory::paginate(Transaction::withScope(OrderByTimestampScope::class)->paginate())->items() as $transaction) {
        $component->assertDontSee($transaction->id());
        $component->assertDontSee($transaction->timestamp());
        $component->assertDontSee($transaction->sender()->address());
        $component->assertDontSee('481.00');
    }

    $component->dispatch('echo:transactions,NewTransaction');

    foreach (ViewModelFactory::paginate(Transaction::withScope(OrderByTimestampScope::class)->paginate())->items() as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee('481.00');
    }
});
