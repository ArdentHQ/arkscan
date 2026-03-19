<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia as Assert;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $this->get(route('bookmarks'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Bookmarks/Index'));
});
