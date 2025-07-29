<?php

declare(strict_types=1);

use App\Models\State;

it('gets the latest state', function () {
    $latestState = State::factory()->create([
        'id' => 1,
    ]);

    State::factory()->create([
        'id' => 2,
    ]);

    expect(State::latest()->is($latestState))->toBeTrue();
});
