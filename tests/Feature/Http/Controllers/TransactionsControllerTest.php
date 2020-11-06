<?php

declare(strict_types=1);

it('should render the page without any errors', function () {
    $this
        ->get(route('transactions'))
        ->assertOk()
        ->assertViewHas('transactionTypeFilter', 'all');
});

it('should render the page with given transaction type', function () {
    $this
        ->get(route('transactions', ['state[type]' => 'multiPayment']))
        ->assertOk()
        ->assertViewHas('transactionTypeFilter', 'multiPayment');
});
