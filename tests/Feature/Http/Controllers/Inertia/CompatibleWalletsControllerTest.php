<?php

declare(strict_types=1);

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Huddle\Zendesk\Facades\Zendesk;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;
use Zendesk\API\Exceptions\ApiResponseException;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $this->get(route('compatible-wallets'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Resources/CompatibleWallets')
            ->where('wallets', array_values(trans("pages.compatible-wallets.wallets"))));
});

it('should be possible to successfully send the form', function () {
    Mail::fake();

    $this->post(route('compatible-wallets.submit'), [
        'name'    => 'test',
        'website' => 'http://www.ardenthq.com',
        'subject' => 'general',
        'message' => 'test',
    ])->assertRedirect(route('compatible-wallets'));
});

it('should show validation error if validation fails', function () {
    $this->post(route('compatible-wallets.submit'), [
        'name'    => 'test',
        'website' => 'test',
        'subject' => 'general',
        'message' => 'test',
    ])->assertSessionHasErrors(['website']);
});
