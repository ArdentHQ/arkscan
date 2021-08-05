<?php

declare(strict_types=1);

it('should render the page without any errors', function () {
    $this
        ->get(route('home'))
        ->assertOk();
});
