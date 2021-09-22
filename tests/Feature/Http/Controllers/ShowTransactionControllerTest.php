<?php

declare(strict_types=1);

use App\Models\Transaction;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $this
        ->get(route('transaction', Transaction::factory()->transfer()->create()))
        ->assertOk();
});
