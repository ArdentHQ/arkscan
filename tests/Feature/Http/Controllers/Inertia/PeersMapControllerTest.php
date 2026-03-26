<?php

declare(strict_types=1);

use App\Models\Peer;
use Inertia\Testing\AssertableInertia as Assert;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $this->get(route('peers-map'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('PeersMap/Index'));
});

it('should return peers with coordinates', function () {
    $this->withoutExceptionHandling();

    $peer = Peer::factory()->create([
        'latitude'  => 50.4777,
        'longitude' => 12.3649,
    ]);

    Peer::factory()->create([
        'latitude'  => null,
        'longitude' => null,
    ]);

    $this->get(route('peers-map'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('PeersMap/Index')
            ->has('peers', 1)
            ->where('peers.0.latitude', $peer->latitude)
            ->where('peers.0.longitude', $peer->longitude));
});
