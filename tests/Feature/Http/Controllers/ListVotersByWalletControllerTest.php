<?php

declare(strict_types=1);

use App\Models\Wallet;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $this
        ->get(route('wallet.voters', Wallet::factory()->create()))
        ->assertOk();
});
