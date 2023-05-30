<?php

declare(strict_types=1);

use App\Models\Wallet;

it('should render the page without any errors for delegates', function () {
    $this
        ->get(route('wallet.voters', Wallet::factory()->activeDelegate()->create()))
        ->assertOk();
});

it('should render a 404 page if wallet is not delegate', function () {
    $this
        ->get(route('wallet.voters', Wallet::factory()->create([
            'attributes' => [],
        ])))
        ->assertNotFound();
});
