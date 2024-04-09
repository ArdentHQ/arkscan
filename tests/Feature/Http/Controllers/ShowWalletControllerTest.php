<?php

declare(strict_types=1);

use App\Facades\Settings;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\ValidatorCache;
use App\ViewModels\ViewModelFactory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Compilers\BladeCompiler;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $wallet = Wallet::factory()->create();

    (new NetworkCache())->setSupply(fn () => '10000000000');

    ((new ValidatorCache())->setTotalAmounts(fn () => [$wallet->public_key => '1000000000']));
    ((new ValidatorCache())->setTotalFees(fn () => [$wallet->public_key => '1000000000']));
    ((new ValidatorCache())->setTotalRewards(fn () => [$wallet->public_key => '1000000000']));
    ((new ValidatorCache())->setTotalBlocks(fn () => [$wallet->public_key => '1000000000']));

    $this
        ->get(route('wallet', $wallet))
        ->assertOk();
});

it('can lookup wallets by the username', function () {
    $this->withoutExceptionHandling();

    $wallet   = Wallet::factory()->create();
    $username = $wallet->attributes['username'];

    (new NetworkCache())->setSupply(fn () => '10000000000');

    ((new ValidatorCache())->setTotalAmounts(fn () => [$wallet->public_key => '1000000000']));
    ((new ValidatorCache())->setTotalFees(fn () => [$wallet->public_key => '1000000000']));
    ((new ValidatorCache())->setTotalRewards(fn () => [$wallet->public_key => '1000000000']));
    ((new ValidatorCache())->setTotalBlocks(fn () => [$wallet->public_key => '1000000000']));

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
        ->assertSee($wallet->username())
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
        ->assertSee($wallet->username())
        ->assertSeeInOrder([
            $wallet->balance->toFloat(),
            '£0.00',
            'GBP',
            'Voting For',
        ]);
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
        ->assertSee($wallet->username())
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

it('should get query data from referer', function () {
    $wallet = Wallet::factory()->create([
        'attributes' => [
            'validatorVoteBalance'    => 1234037456742,
            'validatorProducedBlocks' => 12340,
        ],
    ]);

    $this
        ->withHeaders(['Referer' => 'https://explorer.url?page=5&perPage=10'])
        ->get(route('wallet', $wallet))
        ->assertSee($wallet->address);
});

it('should handle referer without a query string', function () {
    $wallet = Wallet::factory()->create([
        'attributes' => [
            'validatorVoteBalance'    => 1234037456742,
            'validatorProducedBlocks' => 12340,
        ],
    ]);

    $this
        ->withHeaders(['Referer' => 'https://explorer.url'])
        ->get(route('wallet', $wallet))
        ->assertSee($wallet->address);
});

it('should handle no referer', function () {
    $wallet = Wallet::factory()->create([
        'attributes' => [
            'validatorVoteBalance'    => 1234037456742,
            'validatorProducedBlocks' => 12340,
        ],
    ]);

    $this
        ->withHeaders(['Referer' => ''])
        ->get(route('wallet', $wallet))
        ->assertSee($wallet->address);
});

it('should not trim 0 at the end of votes or total forged', function () {
    $wallet = Wallet::factory()->activeValidator()->create([
        'attributes' => [
            'validatorPublicKey'      => 'publicKey',
            'validatorVoteBalance'    => 1234037456742,
            'validatorProducedBlocks' => 12340,
        ],
    ]);

    (new ValidatorCache())->setTotalFees(fn () => [$wallet->public_key => 234037456741]);
    (new ValidatorCache())->setTotalRewards(fn () => [$wallet->public_key => 1000000000001]);

    $this
        ->get(route('wallet', $wallet))
        ->assertSee($wallet->username())
        ->assertSeeInOrder([
            '12,340 DARK',
            'Vote',
        ])
        ->assertSeeInOrder([
            'Total Forged',
            '12,340 DARK',
        ]);
});
