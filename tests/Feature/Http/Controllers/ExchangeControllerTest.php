<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Artisan::call('migrate:fresh');
});

it('should render the page', function () {
    $this->withoutExceptionHandling();

    $this
        ->get(route('exchanges-old'))
        ->assertOk();
});

it('should redirect if network can not exchange', function () {
    Config::set('arkscan.networks.development.canBeExchanged', false);

    $this
        ->get(route('exchanges-old'))
        ->assertRedirect(route('home'));
});
