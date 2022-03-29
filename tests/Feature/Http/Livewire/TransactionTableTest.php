<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Facades\Settings;
use App\Http\Livewire\TransactionTable;
use App\Models\Block;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\CryptoDataCache;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use Ramsey\Uuid\Uuid;

it('should list the first page of records', function () {
    Transaction::factory(30)->transfer()->create();

    $component = Livewire::test(TransactionTable::class);

    foreach (ViewModelFactory::paginate(Transaction::withScope(OrderByTimestampScope::class)->paginate())->items() as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
    }
});

it('should apply filters', function () {
    $block  = Block::factory()->create();
    $wallet = Wallet::factory()->create();

    $component = Livewire::test(TransactionTable::class);

    $notExpected = Transaction::factory(10)->transfer()->create([
        'id'                => (string) Uuid::uuid4(),
        'block_id'          => $block->id,
        'sender_public_key' => $wallet->public_key,
        'recipient_id'      => $wallet->address,
        'timestamp'         => 112982056,
        'fee'               => 1e8,
        'amount'            => 1e8,
    ]);

    foreach (ViewModelFactory::collection($notExpected) as $transaction) {
        $component->assertDontSee($transaction->id());
        $component->assertDontSee($transaction->timestamp());
        $component->assertDontSee($transaction->sender()->address());
        $component->assertDontSee($transaction->recipient()->address());
        $component->assertDontSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
        $component->assertDontSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
    }

    $expected = Transaction::factory(10)->vote()->create(['asset' => null]);

    $component->set('state.type', 'vote');

    foreach (ViewModelFactory::collection($expected) as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
    }
});

it('should apply filters through an event', function () {
    $block  = Block::factory()->create();
    $wallet = Wallet::factory()->create();

    $component = Livewire::test(TransactionTable::class);

    $notExpected = Transaction::factory(10)->transfer()->create([
        'id'                => (string) Uuid::uuid4(),
        'block_id'          => $block->id,
        'sender_public_key' => $wallet->public_key,
        'recipient_id'      => $wallet->address,
        'timestamp'         => 112982056,
        'fee'               => 1e8,
        'amount'            => 1e8,
    ]);

    foreach (ViewModelFactory::collection($notExpected) as $transaction) {
        $component->assertDontSee($transaction->id());
        $component->assertDontSee($transaction->timestamp());
        $component->assertDontSee($transaction->sender()->address());
        $component->assertDontSee($transaction->recipient()->address());
        $component->assertDontSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
        $component->assertDontSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
    }

    $expected = Transaction::factory(10)->vote()->create(['asset' => null]);

    $component->set('state.type', 'vote');

    foreach (ViewModelFactory::collection($expected) as $transaction) {
        $component->assertSee($transaction->id());
        $component->assertSee($transaction->timestamp());
        $component->assertSee($transaction->sender()->address());
        $component->assertSee($transaction->recipient()->address());
        $component->assertSee(NumberFormatter::currency($transaction->fee(), Network::currency()));
        $component->assertSee(NumberFormatter::currency($transaction->amount(), Network::currency()));
    }
});

it('should update the records fiat tooltip when currency changed', function () {
    Config::set('explorer.networks.development.canBeExchanged', true);

    (new CryptoDataCache())->setPrices('USD.week', collect([
        '2020-10-19' => 24210,
    ]));

    (new CryptoDataCache())->setPrices('BTC.week', collect([
        '2020-10-19' => 0.1234567,
    ]));

    Transaction::factory()->create([
        'timestamp'         => 112982056,
        'amount'            => 499 * 1e8,
    ]);

    $component = Livewire::test(TransactionTable::class);

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
