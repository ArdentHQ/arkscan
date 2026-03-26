<?php

declare(strict_types=1);

use App\Models\Peer;
use App\Services\PeersSyncer;
use Illuminate\Support\Facades\Http;
use Torann\GeoIP\Location;

function mockGeoIP(Location $location): void
{
    $mock = Mockery::mock('geoip');
    $mock->shouldReceive('getLocation')->andReturn($location);
    app()->instance('geoip', $mock);
}

function mockGeoIPFailure(): void
{
    $mock = Mockery::mock('geoip');
    $mock->shouldReceive('getLocation')->andThrow(new RuntimeException('GeoIP failed'));
    app()->instance('geoip', $mock);
}

it('should sync new peers', function () {
    Http::fake([
        '*/peers*' => Http::response([
            'data' => [
                ['ip' => '185.220.101.1', 'port' => 4000],
                ['ip' => '195.201.175.10', 'port' => 4000],
            ],
            'meta' => ['last' => 1],
        ]),
    ]);

    mockGeoIP(new Location([
        'ip'      => '185.220.101.1',
        'lat'     => 52.52,
        'lon'     => 13.405,
        'country' => 'Germany',
        'city'    => 'Berlin',
        'default' => false,
    ]));

    $count = (new PeersSyncer())->sync();

    expect($count)->toBe(2);
    expect(Peer::count())->toBe(2);
    expect(Peer::first()->country)->toBe('Germany');
});

it('should handle pagination across multiple pages', function () {
    $callCount = 0;

    Http::fake(function ($request) use (&$callCount) {
        $callCount++;

        if ($callCount === 1) {
            return Http::response([
                'data' => [
                    ['ip' => '185.220.101.1', 'port' => 4000],
                ],
                'meta' => ['last' => 2],
            ]);
        }

        return Http::response([
            'data' => [
                ['ip' => '195.201.175.10', 'port' => 4000],
            ],
            'meta' => ['last' => 2],
        ]);
    });

    mockGeoIP(new Location([
        'lat'     => 52.52,
        'lon'     => 13.405,
        'country' => 'Germany',
        'city'    => 'Berlin',
        'default' => false,
    ]));

    $count = (new PeersSyncer())->sync();

    expect($count)->toBe(2);
    expect(Peer::count())->toBe(2);
});

it('should remove peers no longer in the API', function () {
    Peer::factory()->create(['ip' => '1.2.3.4']);

    Http::fake([
        '*/peers*' => Http::response([
            'data' => [
                ['ip' => '5.6.7.8', 'port' => 4000],
            ],
            'meta' => ['last' => 1],
        ]),
    ]);

    mockGeoIP(new Location([
        'default' => false,
        'lat'     => 40.71,
        'lon'     => -74.00,
        'country' => 'United States',
        'city'    => 'New York',
    ]));

    (new PeersSyncer())->sync();

    expect(Peer::where('ip', '1.2.3.4')->exists())->toBeFalse();
    expect(Peer::where('ip', '5.6.7.8')->exists())->toBeTrue();
});

it('should not re-create existing peers', function () {
    Peer::factory()->create(['ip' => '185.220.101.1']);

    Http::fake([
        '*/peers*' => Http::response([
            'data' => [
                ['ip' => '185.220.101.1', 'port' => 4000],
            ],
            'meta' => ['last' => 1],
        ]),
    ]);

    $count = (new PeersSyncer())->sync();

    expect($count)->toBe(0);
    expect(Peer::count())->toBe(1);
});

it('should return 0 when API fails', function () {
    Http::fake([
        '*/peers*' => Http::response(fn () => throw new Exception('Connection failed')),
    ]);

    expect((new PeersSyncer())->sync())->toBe(0);
});

it('should handle geoip failures gracefully', function () {
    Http::fake([
        '*/peers*' => Http::response([
            'data' => [
                ['ip' => '185.220.101.1', 'port' => 4000],
            ],
            'meta' => ['last' => 1],
        ]),
    ]);

    mockGeoIPFailure();

    $count = (new PeersSyncer())->sync();

    expect($count)->toBe(1);

    $peer = Peer::first();

    expect($peer->latitude)->toBeNull();
    expect($peer->longitude)->toBeNull();
});

it('should handle default location', function () {
    Http::fake([
        '*/peers*' => Http::response([
            'data' => [
                ['ip' => '127.0.0.1', 'port' => 4000],
            ],
            'meta' => ['last' => 1],
        ]),
    ]);

    mockGeoIP(new Location([
        'default' => true,
        'lat'     => 41.31,
        'lon'     => -72.92,
        'country' => 'United States',
        'city'    => 'New Haven',
    ]));

    (new PeersSyncer())->sync();

    $peer = Peer::first();

    expect($peer->latitude)->toBeNull();
    expect($peer->longitude)->toBeNull();
    expect($peer->country)->toBeNull();
});
