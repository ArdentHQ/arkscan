<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Cache::tags('statistics')->flush();
});

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $this
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Home/Index'));
});
