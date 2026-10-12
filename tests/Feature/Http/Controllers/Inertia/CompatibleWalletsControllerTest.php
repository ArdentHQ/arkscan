<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia as Assert;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $this->get(route('compatible-wallets'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Resources/CompatibleWallets')
            ->where('wallets', array_values(trans('pages.compatible-wallets.wallets'))));
});
