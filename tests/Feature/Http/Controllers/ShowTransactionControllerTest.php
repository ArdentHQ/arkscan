<?php

declare(strict_types=1);

use App\Models\Transaction;
use function Tests\fakeCryptoCompare;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    fakeCryptoCompare();

    $this
        ->get(route('old-transaction', Transaction::factory()->create()->hash))
        ->assertOk();
});
