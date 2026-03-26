<?php

declare(strict_types=1);

use App\DTO\Inertia\Peer as PeerDTO;
use App\Models\Peer;

it('should create from model', function () {
    $peer = Peer::factory()->create([
        'ip'        => '185.220.101.1',
        'port'      => 4000,
        'latitude'  => 52.52,
        'longitude' => 13.405,
        'country'   => 'Germany',
        'city'      => 'Berlin',
    ]);

    $dto = PeerDTO::fromModel($peer);

    expect($dto->latitude)->toBe(52.52);
    expect($dto->longitude)->toBe(13.405);
    expect($dto->country)->toBe('Germany');
    expect($dto->city)->toBe('Berlin');
});

it('should handle nullable fields', function () {
    $peer = Peer::factory()->create([
        'latitude'  => null,
        'longitude' => null,
        'country'   => null,
        'city'      => null,
    ]);

    $dto = PeerDTO::fromModel($peer);

    expect($dto->latitude)->toBeNull();
    expect($dto->longitude)->toBeNull();
    expect($dto->country)->toBeNull();
    expect($dto->city)->toBeNull();
});
