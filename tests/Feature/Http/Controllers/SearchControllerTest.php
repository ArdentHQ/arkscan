<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use App\Services\Identity;
use Illuminate\Support\Facades\Cache;

it('returns an empty result set when the query is missing', function () {
    $this
        ->getJson(route('navbar-search.index'))
        ->assertOk()
        ->assertJson([
            'results'    => [],
            'hasResults' => false,
        ]);
});

it('returns wallet results matching the query', function () {
    $wallet = Wallet::factory()->create();
    Wallet::factory()->create();

    $response = $this
        ->getJson(route('navbar-search.index', ['query' => $wallet->address]))
        ->assertOk()
        ->assertJson([
            'hasResults' => true,
        ]);

    expect($response->json('results.0.identifier'))->toBe($wallet->address);
    expect($response->json('results.0.type'))->toBe('wallet');
    expect($response->json('results.0.data.address'))->toBe($wallet->address);
});

it('redirects to the first result when available', function () {
    $block = Block::factory()->create();

    $this
        ->post(route('navbar-search.redirect'), ['query' => $block->hash])
        ->assertRedirect(route('block', $block));
});

it('returns no content when trying to redirect without results', function () {
    $this
        ->post(route('navbar-search.redirect'), ['query' => 'unknown'])
        ->assertNoContent();
});

it('returns block results including validator wallet metadata', function () {
    Cache::tags('wallet')->flush();

    $validator = Wallet::factory()->create([
        'address' => '0x'.str_repeat('1', 40),
    ]);

    $walletCache = new WalletCache();
    $walletCache->setWalletNameByAddress($validator->address, 'Validator One');
    $walletCache->setContractAddresses([$validator->address]);

    $block = Block::factory()->create([
        'hash'               => str_repeat('a', 64),
        'proposer'           => $validator->address,
        'transactions_count' => 12,
    ]);

    $response = $this
        ->getJson(route('navbar-search.index', ['query' => $block->hash]))
        ->assertOk();

    $blockResult = collect($response->json('results'))
        ->first(fn ($result) => $result['type'] === 'block');

    expect($blockResult)->not->toBeNull();
    expect($blockResult['type'])->toBe('block');
    expect($blockResult['identifier'])->toBe($block->hash);
    expect($blockResult['data']['hash'])->toBe($block->hash);
    expect($blockResult['data']['transactionCount'])->toBe(12);
    expect($blockResult['data']['validator']['address'])->toBe($validator->address);
    expect($blockResult['data']['validator']['username'])->toBe('Validator One');
    expect($blockResult['data']['validator']['isContract'])->toBeTrue();
});

it('returns transaction results including vote metadata', function () {
    Cache::tags('wallet')->flush();
    Cache::tags('identity')->flush();

    $walletCache = new WalletCache();

    $sender    = Wallet::factory()->create();
    $recipient = Wallet::factory()->create();
    $delegate  = Wallet::factory()->create();

    $senderAddress = Identity::address($sender->public_key);
    $walletCache->setWalletNameByAddress($senderAddress, 'Sender Wallet');
    $walletCache->setWalletNameByAddress($recipient->address, 'Recipient Wallet');
    $walletCache->setContractAddresses([$recipient->address]);

    $transaction = Transaction::factory()
        ->vote($delegate->address)
        ->create([
            'hash'              => str_repeat('b', 64),
            'sender_public_key' => $sender->public_key,
            'from'              => $sender->address,
            'to'                => $recipient->address,
        ]);

    $response = $this
        ->getJson(route('navbar-search.index', ['query' => $transaction->hash]))
        ->assertOk();

    $transactionResult = collect($response->json('results'))
        ->first(fn ($result) => $result['type'] === 'transaction');

    expect($transactionResult)->not->toBeNull();
    expect($transactionResult['type'])->toBe('transaction');
    expect($transactionResult['identifier'])->toBe($transaction->hash);
    expect($transactionResult['data']['hash'])->toBe($transaction->hash);
    expect($transactionResult['data']['isVote'])->toBeTrue();
    expect($transactionResult['data']['isTransfer'])->toBeFalse();
    expect($transactionResult['data']['sender']['address'])->toBe($senderAddress);
    expect($transactionResult['data']['sender']['username'])->toBe('Sender Wallet');
    expect($transactionResult['data']['recipient']['address'])->toBe($recipient->address);
    expect($transactionResult['data']['recipient']['username'])->toBe('Recipient Wallet');
    expect($transactionResult['data']['recipient']['isContract'])->toBeTrue();
    expect($transactionResult['data']['votedValidatorLabel'])->toBe(
        $delegate->username() ?? $delegate->address
    );
});
