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

    (new NetworkCache())->setSupply(fn () => 100 * 1e18);

    ((new ValidatorCache())->setTotalFees([$wallet->address => 10 * 1e18]));
    ((new ValidatorCache())->setTotalRewards([$wallet->address => 10 * 1e18]));
    ((new ValidatorCache())->setTotalBlocks([$wallet->address => 10 * 1e18]));

    $this
        ->get(route('wallet', $wallet))
        ->assertOk();
});

it('can lookup wallets by the username', function () {
    $this->withoutExceptionHandling();

    $wallet   = Wallet::factory()->create();
    $username = $wallet->attributes['username'];

    (new NetworkCache())->setSupply(fn () => 100 * 1e18);

    ((new ValidatorCache())->setTotalFees([$wallet->address => 10 * 1e18]));
    ((new ValidatorCache())->setTotalRewards([$wallet->address => 10 * 1e18]));
    ((new ValidatorCache())->setTotalBlocks([$wallet->address => 10 * 1e18]));

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
        ->andReturnNull()
        ->shouldReceive('all')
        ->andReturn([]);

    $wallet = Wallet::factory()->create();

    $response = $this
        ->get(route('wallet', $wallet))
        ->assertSee($wallet->address)
        ->assertSee('0 BTC');

    $content = preg_replace('/\s+/', ' ', str_replace("\n", '', strip_tags($response->getContent())));

    expect($content)->not->toContain('0 BTC BTC Voting For');
});

it('should show currency symbol and code for crypto', function () {
    Config::set('arkscan.networks.development.canBeExchanged', true);

    Settings::shouldReceive('currency')
        ->andReturn('GBP')
        ->shouldReceive('get')
        ->andReturnNull()
        ->shouldReceive('all')
        ->andReturn([]);

    $wallet = Wallet::factory()->create();

    $response = $this
        ->get(route('wallet', $wallet))
        ->assertSee($wallet->address)
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
        ->andReturnNull()
        ->shouldReceive('all')
        ->andReturn([]);

    $wallet = Wallet::factory()->create();

    $this
        ->get(route('wallet', $wallet))
        ->assertSee($wallet->address)
        ->assertSeeInOrder([
            'Value',
            'N/A',
            'Voting For',
        ]);
});

it('should filter transactions via url', function () {
    $wallet = Wallet::factory()->create();

    $transaction = Transaction::factory()->transfer()->create([
        'sender_public_key' => $wallet->public_key,
    ]);

    Route::get('/test-transactions/{wallet}', function () use ($wallet) {
        return BladeCompiler::render('<livewire:wallet.tabs :wallet="$wallet" :defer-loading="false" />', ['wallet' => ViewModelFactory::make($wallet)]);
    });

    $this
        ->get('/test-transactions/'.$wallet->address)
        ->assertOk()
        ->assertSee($transaction->hash);

    $this
        ->get('/test-transactions/'.$wallet->address.'?outgoing=false')
        ->assertOk()
        ->assertDontSee($transaction->hash);

    $this
        ->get('/test-transactions/'.$wallet->address.'?outgoing=0')
        ->assertOk()
        ->assertDontSee($transaction->hash);

    $this
        ->get('/test-transactions/'.$wallet->address.'?outgoing=1')
        ->assertOk()
        ->assertSee($transaction->hash);
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
            'validatorVoteBalance'    => 12340.37456742 * 1e18,
            'validatorProducedBlocks' => 12340,
        ],
    ]);

    (new ValidatorCache())->setTotalFees([$wallet->address => 2340.37456741 * 1e18]);
    (new ValidatorCache())->setTotalRewards([$wallet->address => 10000.00000001 * 1e18]);

    $this
        ->get(route('wallet', $wallet))
        ->assertSee($wallet->address)
        ->assertSeeInOrder([
            '12,340 DARK',
            'Vote',
        ])
        ->assertSeeInOrder([
            'Total Forged',
            '12,340 DARK',
        ]);
});

it('should show resigned status for validators', function () {
    $wallet = Wallet::factory()->create([
        'attributes' => [
            'validatorPublicKey'      => 'publicKey',
            'validatorVoteBalance'    => 12340.37456742 * 1e18,
            'validatorProducedBlocks' => 12340,
            'validatorResigned'       => true,
        ],
    ]);

    (new ValidatorCache())->setTotalFees([$wallet->address => 2340.37456741 * 1e18]);
    (new ValidatorCache())->setTotalRewards([$wallet->address => 10000.00000001 * 1e18]);

    $this
        ->get(route('wallet', $wallet))
        ->assertSee($wallet->address)
        ->assertSee('Resigned')
        ->assertDontSee('Active')
        ->assertDontSee('Standby')
        ->assertDontSee('Dormant');
});

it('should show dormant status for validators', function () {
    $wallet = Wallet::factory()->create([
        'attributes' => [
            'validatorPublicKey' => null,
        ],
    ]);

    $this
        ->get(route('wallet', $wallet))
        ->assertSee($wallet->address)
        ->assertSee('Dormant')
        ->assertDontSee('Active')
        ->assertDontSee('Standby')
        ->assertDontSee('Resigned');
});

it('should show standby status for validators', function () {
    $wallet = Wallet::factory()->standbyValidator()->create([
        'attributes' => [
            'validatorPublicKey' => 'publicKey',
            'validatorRank'      => 54,
        ],
    ]);

    $this
        ->get(route('wallet', $wallet))
        ->assertSee($wallet->address)
        ->assertSee('Standby')
        ->assertDontSee('Active')
        ->assertDontSee('Resigned')
        ->assertDontSee('Dormant');
});
