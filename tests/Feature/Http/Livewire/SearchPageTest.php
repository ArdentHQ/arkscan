<?php

declare(strict_types=1);

use App\Http\Livewire\SearchPage;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use App\Services\Timestamp;
use Carbon\Carbon;
use Livewire\Livewire;

beforeEach(function () {
    (new NetworkCache())->setSupply(fn () => strval(10e8));
});

it('should search for blocks', function () {
    $block = Block::factory()->create();

    Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.term', $block->id)
        ->call('performSearch')
        ->assertSee($block->id);
});

it('should search for transactions', function () {
    $transaction = Transaction::factory()->transfer()->create();

    Livewire::test(SearchPage::class)
        ->set('state.type', 'transaction')
        ->set('state.term', $transaction->id)
        ->call('performSearch')
        ->assertSee($transaction->id);
});

it('should search for wallets', function () {
    $wallet = Wallet::factory()->create();

    Livewire::test(SearchPage::class)
        ->set('state.type', 'wallet')
        ->set('state.term', $wallet->address)
        ->call('performSearch')
        ->assertSee($wallet->address);
});

it('should search for a block by id', function () {
    $block = Block::factory(10)->create()[0];

    $results = Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.term', $block->id)
        ->call('performSearch')
        ->assertSee($block->id);
});

it('should search for blocks by timestamp minimum', function () {
    $today        = Carbon::now();
    $todayGenesis = Timestamp::fromUnix($today->unix())->unix();

    $yesterday        = Carbon::now()->subDay();
    $yesterdayGenesis = Timestamp::fromUnix($yesterday->unix())->unix();

    $blocks = Block::factory(10)->create(['timestamp' => $todayGenesis]);
    Block::factory(10)->create(['timestamp' => $yesterdayGenesis]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.dateFrom', $today->toString())
        ->call('performSearch')
        ->assertSeeInOrder($blocks->pluck('id')->toArray());
});

it('should search for blocks by timestamp maximum', function () {
    $today        = Carbon::now();
    $todayGenesis = Timestamp::fromUnix($today->unix())->unix();

    $yesterday        = Carbon::now()->subDay();
    $yesterdayGenesis = Timestamp::fromUnix($yesterday->unix())->unix();

    Block::factory(10)->create(['timestamp' => $todayGenesis]);
    $blocks = Block::factory(10)->create(['timestamp' => $yesterdayGenesis]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.dateTo', $yesterday->toString())
        ->call('performSearch')
        ->assertSeeInOrder($blocks->pluck('id')->toArray());
});

it('should search for blocks by timestamp range', function () {
    $today        = Carbon::now();
    $todayGenesis = Timestamp::fromUnix($today->unix())->unix();

    $yesterday        = Carbon::now()->subDay();
    $yesterdayGenesis = Timestamp::fromUnix($yesterday->unix())->unix();

    Block::factory(10)->create(['timestamp' => $todayGenesis]);
    $blocks = Block::factory(10)->create(['timestamp' => $yesterdayGenesis]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.dateFrom', $yesterday->toString())
        ->set('state.dateTo', $yesterday->toString())
        ->call('performSearch')
        ->assertSeeInOrder($blocks->pluck('id')->toArray());
});

it('should search for blocks by total_amount minimum', function () {
    Block::factory(10)->create(['total_amount' => 1000 * 1e8]);
    $blocks = Block::factory(10)->create(['total_amount' => 2000 * 1e8]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.totalAmountFrom', 2000)
        ->call('performSearch')
        ->assertSeeInOrder($blocks->pluck('id')->toArray());
});

it('should search for blocks by total_amount maximum', function () {
    $blocks = Block::factory(10)->create(['total_amount' => 1000 * 1e8]);
    Block::factory(10)->create(['total_amount' => 2000 * 1e8]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.totalAmountTo', 1000)
        ->call('performSearch')
        ->assertSeeInOrder($blocks->pluck('id')->toArray());
});

it('should search for blocks by total_amount range', function () {
    $blocks = Block::factory(10)->create(['total_amount' => 1000 * 1e8]);
    Block::factory(10)->create(['total_amount' => 2000 * 1e8]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.totalAmountFrom', 500)
        ->set('state.totalAmountTo', 1500)
        ->call('performSearch')
        ->assertSeeInOrder($blocks->pluck('id')->toArray());
});

it('should search for blocks by total_fee minimum', function () {
    Block::factory(10)->create(['total_fee' => 1000 * 1e8]);
    $blocks = Block::factory(10)->create(['total_fee' => 2000 * 1e8]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.totalFeeFrom', 2000)
        ->call('performSearch')
        ->assertSeeInOrder($blocks->pluck('id')->toArray());
});

it('should search for blocks by total_fee maximum', function () {
    $blocks = Block::factory(10)->create(['total_fee' => 1000 * 1e8]);
    Block::factory(10)->create(['total_fee' => 2000 * 1e8]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.totalFeeTo', 2000)
        ->call('performSearch')
        ->assertSeeInOrder($blocks->pluck('id')->toArray());
});

it('should search for blocks by total_fee range', function () {
    $blocks = Block::factory(10)->create(['total_fee' => 1000 * 1e8]);
    Block::factory(10)->create(['total_fee' => 2000 * 1e8]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.totalFeeFrom', 500)
        ->set('state.totalFeeTo', 1500)
        ->call('performSearch')
        ->assertSeeInOrder($blocks->pluck('id')->toArray());
});

it('should search for blocks by height minimum', function () {
    $heightStart = 1000;
    $heightEnd   = 2000;

    Block::factory(10)->create(['height' => $heightStart]);
    $blocks = Block::factory(10)->create(['height' => $heightEnd]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.heightFrom', $heightEnd)
        ->call('performSearch')
        ->assertSeeInOrder($blocks->pluck('id')->toArray());
});

it('should search for blocks by height maximum', function () {
    $heightStart = 1000;
    $heightEnd   = 2000;

    $blocks = Block::factory(10)->create(['height' => $heightStart]);
    Block::factory(10)->create(['height' => $heightEnd]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.heightTo', $heightStart)
        ->call('performSearch')
        ->assertSeeInOrder($blocks->pluck('id')->toArray());
});

it('should search for blocks by height range', function () {
    $heightStart = 1000;
    $heightEnd   = 2000;

    $blocks = Block::factory(10)->create(['height' => $heightStart]);
    Block::factory(10)->create(['height' => $heightEnd]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.heightFrom', 500)
        ->set('state.heightTo', 1500)
        ->call('performSearch')
        ->assertSeeInOrder($blocks->pluck('id')->toArray());
});

it('should search for blocks by reward range minimum', function () {
    Block::factory(10)->create(['reward' => 1000 * 1e8]);
    $blocks = Block::factory(10)->create(['reward' => 2000 * 1e8]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.rewardFrom', 2000)
        ->call('performSearch')
        ->assertSeeInOrder($blocks->pluck('id')->toArray());
});

it('should search for blocks by reward range maximum', function () {
    $blocks = Block::factory(10)->create(['reward' => 1000 * 1e8]);
    Block::factory(10)->create(['reward' => 2000 * 1e8]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.rewardTo', 1000)
        ->call('performSearch')
        ->assertSeeInOrder($blocks->pluck('id')->toArray());
});

it('should search for blocks by reward range range', function () {
    $blocks = Block::factory(10)->create(['reward' => 1000 * 1e8]);
    Block::factory(10)->create(['reward' => 2000 * 1e8]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.rewardFrom', 500)
        ->set('state.rewardTo', 1500)
        ->call('performSearch')
        ->assertSeeInOrder($blocks->pluck('id')->toArray());
});

it('should search for blocks by generator with an address', function () {
    Block::factory(10)->create();

    $wallet = Wallet::factory()->create();

    $block = Block::factory()->create([
        'generator_public_key' => $wallet->public_key,
    ]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.term', $wallet->address)
        ->call('performSearch')
        ->assertSee($block->id);
});

it('should search for blocks by generator with a public key', function () {
    Block::factory(10)->create();

    $wallet = Wallet::factory()->create();

    $block = Block::factory()->create([
        'generator_public_key' => $wallet->public_key,
    ]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.generatorPublicKey', $wallet->public_key)
        ->call('performSearch')
        ->assertSee($block->id);
});

it('should search for blocks by generator with a username', function () {
    Block::factory(10)->create();

    $wallet = Wallet::factory()->create([
        'attributes' => [
            'delegate' => [
                'username' => 'johndoe',
            ],
        ],
    ]);

    $block = Block::factory()->create([
        'generator_public_key' => $wallet->public_key,
    ]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'block')
        ->set('state.term', 'johndoe')
        ->call('performSearch')
        ->assertSee($block->id);
});

it('should search for a transaction by id', function () {
    $transaction = Transaction::factory(10)->create()[0];

    Livewire::test(SearchPage::class)
        ->set('state.type', 'transaction')
        ->set('state.transactionType', 'all')
        ->set('state.term', $transaction->id)
        ->call('performSearch')
        ->assertSee($transaction->id);
});

it('should search for a transaction by vendor field', function () {
    $transaction = Transaction::factory(10)->create()[0];
    $transaction->update(['vendor_field' => 'Hello World']);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'transaction')
        ->set('state.transactionType', 'all')
        ->set('state.smartBridge', $transaction->vendor_field)
        ->call('performSearch')
        ->assertSee($transaction->id);
});

it('should search for transactions by timestamp minimum', function () {
    $today        = Carbon::now();
    $todayGenesis = Timestamp::fromUnix($today->unix())->unix();

    $yesterday        = Carbon::now()->subDay();
    $yesterdayGenesis = Timestamp::fromUnix($yesterday->unix())->unix();

    $transactions = Transaction::factory(10)->create(['timestamp' => $todayGenesis]);
    Transaction::factory(10)->create(['timestamp' => $yesterdayGenesis]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'transaction')
        ->set('state.transactionType', 'all')
        ->set('state.dateFrom', $today->toString())
        ->call('performSearch')
        ->assertSeeInOrder($transactions->pluck('id')->toArray());
});

it('should search for transactions by timestamp maximum', function () {
    $today        = Carbon::now();
    $todayGenesis = Timestamp::fromUnix($today->unix())->unix();

    $yesterday        = Carbon::now()->subDay();
    $yesterdayGenesis = Timestamp::fromUnix($yesterday->unix())->unix();

    Transaction::factory(10)->create(['timestamp' => $todayGenesis]);
    $transactions = Transaction::factory(10)->create(['timestamp' => $yesterdayGenesis]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'transaction')
        ->set('state.transactionType', 'all')
        ->set('state.dateTo', $yesterday->toString())
        ->call('performSearch')
        ->assertSeeInOrder($transactions->pluck('id')->toArray());
});

it('should search for transactions by timestamp range', function () {
    $today        = Carbon::now();
    $todayGenesis = Timestamp::fromUnix($today->unix())->unix();

    $yesterday        = Carbon::now()->subDay();
    $yesterdayGenesis = Timestamp::fromUnix($yesterday->unix())->unix();

    Transaction::factory(10)->create(['timestamp' => $todayGenesis]);
    $transactions = Transaction::factory(10)->create(['timestamp' => $yesterdayGenesis]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'transaction')
        ->set('state.transactionType', 'all')
        ->set('state.dateFrom', $yesterday->toString())
        ->set('state.dateTo', $yesterday->toString())
        ->call('performSearch')
        ->assertSeeInOrder($transactions->pluck('id')->toArray());
});

it('should search for transactions by amount minimum', function () {
    Transaction::factory(10)->create(['amount' => 1000 * 1e8]);
    $transactions = Transaction::factory(10)->create(['amount' => 2000 * 1e8]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'transaction')
        ->set('state.transactionType', 'all')
        ->set('state.amountFrom', 2000)
        ->call('performSearch')
        ->assertSeeInOrder($transactions->pluck('id')->toArray());
});

it('should search for transactions by amount maximum', function () {
    $transactions = Transaction::factory(10)->create(['amount' => 1000 * 1e8]);
    Transaction::factory(10)->create(['amount' => 2000 * 1e8]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'transaction')
        ->set('state.transactionType', 'all')
        ->set('state.amountTo', 1000)
        ->call('performSearch')
        ->assertSeeInOrder($transactions->pluck('id')->toArray());
});

it('should search for transactions by amount range', function () {
    $transactions = Transaction::factory(10)->create(['amount' => 1000 * 1e8]);
    Transaction::factory(10)->create(['amount' => 2000 * 1e8]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'transaction')
        ->set('state.transactionType', 'all')
        ->set('state.amountFrom', 500)
        ->set('state.amountTo', 1500)
        ->call('performSearch')
        ->assertSeeInOrder($transactions->pluck('id')->toArray());
});

it('should search for multipayment transactions by amount range', function () {
    $transaction = Transaction::factory()->multiPayment()->create([
        'amount' => 0,
        'asset'  => [
            'payments' => [
                ['amount' => 750 * 1e8, 'recipientId' => 'D61mfSggzbvQgTUe6JhYKH2doHaqJ3Dyib'],
                ['amount' => 251 * 1e8, 'recipientId' => 'DFJ5Z51F1euNNdRUQJKQVdG4h495LZkc6T'],
            ],
        ],
    ]);
    $transaction2 = Transaction::factory()->create(['amount' => 2000 * 1e8]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'transaction')
        ->set('state.transactionType', 'multiPayment')
        ->set('state.amountFrom', 900)
        ->set('state.amountTo', 1100)
        ->call('performSearch')
        ->assertSee($transaction->id)
        ->assertDontSee($transaction2->id);
});

it('should search for multipayment transactions by amount range with decimals', function () {
    $transaction = Transaction::factory()->multiPayment()->create([
        'amount' => 0,
        'asset'  => [
            'payments' => [
                ['amount' => 0.45 * 1e8, 'recipientId' => 'D61mfSggzbvQgTUe6JhYKH2doHaqJ3Dyib'],
                ['amount' => 0.50 * 1e8, 'recipientId' => 'DFJ5Z51F1euNNdRUQJKQVdG4h495LZkc6T'],
            ],
        ],
    ]);
    $transaction2 = Transaction::factory()->create(['amount' => 2000 * 1e8]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'transaction')
        ->set('state.transactionType', 'multiPayment')
        ->set('state.amountFrom', 0.9)
        ->set('state.amountTo', 1.1)
        ->call('performSearch')
        ->assertSee($transaction->id)
        ->assertDontSee($transaction2->id);
});

it('should search for transactions by fee minimum', function () {
    Transaction::factory(10)->create(['fee' => 1000 * 1e8]);
    $transactions = Transaction::factory(10)->create(['fee' => 2000 * 1e8]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'transaction')
        ->set('state.transactionType', 'all')
        ->set('state.feeFrom', 2000)
        ->call('performSearch')
        ->assertSeeInOrder($transactions->pluck('id')->toArray());
});

it('should search for transactions by fee maximum', function () {
    $transactions = Transaction::factory(10)->create(['fee' => 1000 * 1e8]);
    Transaction::factory(10)->create(['fee' => 2000 * 1e8]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'transaction')
        ->set('state.transactionType', 'all')
        ->set('state.feeTo', 1000)
        ->call('performSearch')
        ->assertSeeInOrder($transactions->pluck('id')->toArray());
});

it('should search for transactions by fee range', function () {
    $transactions = Transaction::factory(10)->create(['fee' => 1000 * 1e8]);
    Transaction::factory(10)->create(['fee' => 2000 * 1e8]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'transaction')
        ->set('state.transactionType', 'all')
        ->set('state.feeFrom', 500)
        ->set('state.feeTo', 1500)
        ->call('performSearch')
        ->assertSeeInOrder($transactions->pluck('id')->toArray());
});

it('should search for transactions by wallet with an address', function () {
    Transaction::factory(10)->create();

    $wallet = Wallet::factory()->create();

    $tx1 = Transaction::factory()->create([
        'sender_public_key' => $wallet->public_key,
    ]);

    $tx2 = Transaction::factory()->create([
        'recipient_id' => $wallet->address,
    ]);

    $tx3 = Transaction::factory()->create([
        'asset' => [
            'payments' => [
                ['amount' => 10, 'recipientId' => $wallet->address],
            ],
        ],
    ]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'transaction')
        ->set('state.transactionType', 'all')
        ->set('state.term', $wallet->address)
        ->call('performSearch')
        ->assertSee($tx1->id)
        ->assertSee($tx2->id)
        ->assertSee($tx3->id);
});

it('should search for transactions by wallet with a public key', function () {
    Transaction::factory(10)->create();

    $wallet = Wallet::factory()->create();

    $tx1 = Transaction::factory()->create([
        'sender_public_key' => $wallet->public_key,
    ]);

    $tx2 = Transaction::factory()->create([
        'recipient_id' => $wallet->address,
    ]);

    $tx3 = Transaction::factory()->create([
        'asset' => [
            'payments' => [
                ['amount' => 10, 'recipientId' => $wallet->address],
            ],
        ],
    ]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'transaction')
        ->set('state.transactionType', 'all')
        ->set('state.term', $wallet->public_key)
        ->call('performSearch')
        ->assertSee($tx1->id)
        ->assertSee($tx2->id)
        ->assertSee($tx3->id);
});

it('should search for transactions by wallet with a username', function () {
    Transaction::factory(10)->create();

    $wallet = Wallet::factory()->create([
        'attributes' => [
            'delegate' => [
                'username' => 'johndoe',
            ],
        ],
    ]);

    $tx1 = Transaction::factory()->create([
        'sender_public_key' => $wallet->public_key,
    ]);

    $tx2 = Transaction::factory()->create([
        'recipient_id' => $wallet->address,
    ]);

    $tx3 = Transaction::factory()->create([
        'asset' => [
            'payments' => [
                ['amount' => 10, 'recipientId' => $wallet->address],
            ],
        ],
    ]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'transaction')
        ->set('state.transactionType', 'all')
        ->set('state.term', 'johndoe')
        ->call('performSearch')
        ->assertSee($tx1->id)
        ->assertSee($tx2->id)
        ->assertSee($tx3->id);
});

it('should search for transactions by block with an ID', function () {
    Transaction::factory(10)->create();

    $tx = Transaction::factory()->create([
        'block_id' => 'ffff273321907d20bda3278ade259e6364ec2091ecd5993398a2ef2402725a31',
    ]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'transaction')
        ->set('state.transactionType', 'all')
        ->set('state.term', 'ffff273321907d20bda3278ade259e6364ec2091ecd5993398a2ef2402725a31')
        ->call('performSearch')
        ->assertSee($tx->id);
});

it('should search for transactions by block with a height', function () {
    Transaction::factory(10)->create();

    $tx = Transaction::factory()->create([
        'block_height' => 123456789,
    ]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'transaction')
        ->set('state.transactionType', 'all')
        ->set('state.term', '123456789')
        ->call('performSearch')
        ->assertSee($tx->id);
});

it('should search for a wallet by address', function () {
    $wallet = Wallet::factory(10)->create()[0];

    Livewire::test(SearchPage::class)
        ->set('state.type', 'wallet')
        ->set('state.term', $wallet->address)
        ->call('performSearch')
        ->assertSee($wallet->address);
});

it('should search for a wallet by public key', function () {
    Wallet::factory(10)->create(['public_key' => '123']);

    $wallet = Wallet::factory()->create();

    Livewire::test(SearchPage::class)
        ->set('state.type', 'wallet')
        ->set('state.term', $wallet->public_key)
        ->call('performSearch')
        ->assertSee($wallet->address);
});

it('should search for a wallet by delegate username in terms', function () {
    $wallet = Wallet::factory(10)->create()[0];

    Livewire::test(SearchPage::class)
        ->set('state.type', 'wallet')
        ->set('state.term', $wallet->attributes['delegate']['username'])
        ->call('performSearch')
        ->assertSee($wallet->address);
});

it('should search for a wallet by username', function () {
    $wallet = Wallet::factory(10)->create()[0];

    Livewire::test(SearchPage::class)
        ->set('state.type', 'wallet')
        ->set('state.username', $wallet->attributes['delegate']['username'])
        ->call('performSearch')
        ->assertSee($wallet->address);
});

it('should search for a wallet by vote', function () {
    Wallet::factory(10)->create();

    $wallet = Wallet::factory()->create([
        'attributes' => [
            'vote' => 'public_key',
        ],
    ]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'wallet')
        ->set('state.vote', $wallet->attributes['vote'])
        ->call('performSearch')
        ->assertSee($wallet->address);
});

it('should search for a wallet by balance minimum', function () {
    $wallet = Wallet::factory(10)->create(['balance' => 100 * 1e8])[0];
    $wallet->update(['balance' => 1000 * 1e8]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'wallet')
        ->set('state.balanceFrom', 101)
        ->call('performSearch')
        ->assertSee($wallet->address);
});

it('should search for a wallet by balance maximum', function () {
    $wallets = Wallet::factory(10)->create(['balance' => 100 * 1e8]);
    $wallet  = Wallet::factory()->create(['balance' => 100 * 1e8]);
    $wallet->update(['balance' => 1000 * 1e8]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'wallet')
        ->set('state.balanceTo', 999)
        ->call('performSearch')
        ->assertDontSee($wallet->id)
        ->assertSeeInOrder($wallets->pluck('address')->toArray());
});

it('should search for a wallet by balance range', function () {
    $wallets = Wallet::factory(10)->create(['balance' => 10 * 1e8]);
    $wallet  = Wallet::factory(10)->create(['balance' => 100 * 1e8])[0];
    $wallet->update(['balance' => 1000 * 1e8]);

    Livewire::test(SearchPage::class)
        ->set('state.type', 'wallet')
        ->set('state.balanceTo', 50)
        ->set('state.balanceTo', 100)
        ->call('performSearch')
        ->assertDontSee($wallet->id)
        ->assertSeeInOrder($wallets->pluck('address')->toArray());
});
