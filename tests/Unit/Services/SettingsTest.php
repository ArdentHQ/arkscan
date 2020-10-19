<?php

declare(strict_types=1);

use App\Services\Settings;
use Illuminate\Support\Facades\Session;

it('should have all settings with defaults', function () {
    expect(Settings::all())->toBe([
        'currency'        => 'usd',
        'statisticsChart' => true,
        'darkTheme'       => true,
    ]);
});

it('should have all settings with values from a session', function () {
    $settings = [
        'currency'        => 'chf',
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

it('should have a currency setting', function () {
    expect(Settings::currency())->toBe('usd');
});

it('should have a statistics chart setting', function () {
    expect(Settings::statisticsChart())->toBeTrue();
});

it('should have a dark theme setting', function () {
    expect(Settings::darkTheme())->toBeTrue();
});
