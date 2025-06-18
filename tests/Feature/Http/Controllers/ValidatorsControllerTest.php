<?php

declare(strict_types=1);

it('should render the page without any errors', function () {
    $this
        ->get(route('validators'))
        ->assertOk();
});

it('should navigate directly to a different page number', function () {
    $this
        ->get(route('validators', ['page' => 2]))
        ->assertOk();
});
