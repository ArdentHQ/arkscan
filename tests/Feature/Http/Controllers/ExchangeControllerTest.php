<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;

it('should render the page', function () {
    $this
        ->get(route('exchanges'))
        ->assertOk();
});

it('should redirect if network can not exchange', function () {
    Config::set('explorer.networks.development.canBeExchanged', false);

    $this
        ->get(route('exchanges'))
        ->assertRedirect(route('home'));
});
