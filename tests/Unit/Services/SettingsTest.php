<?php

declare(strict_types=1);

use App\Services\Settings;
use Illuminate\Support\Facades\Session;

it('should have all settings with defaults', function () {
    expect(Settings::all())->toBe([
        'language'        => 'en',
        'currency'        => 'usd',
        'priceSource'     => 'cryptocompare',
        'statisticsChart' => true,
        'darkTheme'       => true,
    ]);
});

it('should have all settings with values from a session', function () {
    $settings = [
        'language'        => 'en',
        'currency'        => 'chf',
        'priceSource'     => 'cryptocompare',
        'statisticsChart' => true,
        'darkTheme'       => true,
    ];

    Session::shouldReceive('has')
        ->once()
        ->with('settings')
        ->andReturn(true);

    Session::shouldReceive('get')
        ->once()
        ->with('settings')
        ->andReturn(json_encode($settings));

    expect(Settings::all())->toBe($settings);
});

it('should have a language setting', function () {
    expect(Settings::language())->toBe('en');
});

it('should have a currency setting', function () {
    expect(Settings::currency())->toBe('usd');
});

it('should have a price source setting', function () {
    expect(Settings::priceSource())->toBe('cryptocompare');
});

it('should have a statistics chart setting', function () {
    expect(Settings::statisticsChart())->toBeTrue();
});

it('should have a dark theme setting', function () {
    expect(Settings::darkTheme())->toBeTrue();
});
