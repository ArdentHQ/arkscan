<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Wallet;

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
