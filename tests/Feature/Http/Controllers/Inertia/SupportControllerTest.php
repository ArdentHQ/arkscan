<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia as Assert;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $this->get(route('contact'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Support/Index')
            ->where('contactEmail', config('mail.contact_email'))
            ->where('socialNetworkUrls.twitter', config('social.networks.twitter.url'))
            ->where('socialNetworkUrls.github', config('social.networks.github.url')));
});
