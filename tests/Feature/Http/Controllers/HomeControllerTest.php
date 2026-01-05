<?php

declare(strict_types=1);

use App\Models\State;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    State::factory()->create();

    $this
        ->get(route('home'))
        ->assertOk();
});
