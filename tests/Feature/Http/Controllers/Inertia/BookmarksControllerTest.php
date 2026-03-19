<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->withoutExceptionHandling();
});

it('should render the page without any errors', function () {
    $this->get(route('bookmarks'))
        ->assertOk()
        ->assertInertia(function (Assert $page) {
            $page->component('Bookmarks/Index');
        });
});
