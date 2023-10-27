<?php

declare(strict_types=1);

use App\Facades\Settings;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

// Since the settings rely on the cookies I need to create a fake route that will
// contain the cookie passed as a param and will respond with the data that the
// Settings service returns for the given method. The response will be a json
// that we can use for testing the values.
function getSettingsFromCookies($ctx, $method = 'all', array $settings = [])
{
    Route::get('get-settings', ['middleware' => 'web', fn () => Arr::wrap(Settings::$method())]);

    $response = $ctx->withCookies([
        'settings' => json_encode($settings),
    ])->get('get-settings');

    return is_string($response) ? $response : $response->json();
}

it('should have all settings with defaults', function () {
    expect(Settings::all())->toBe([
        'currency'       => 'USD',
        'priceChart'     => true,
        'feeChart'       => true,
        'darkTheme'      => null,
    ]);
});

it('should have all settings with values from a session', function () {
    $settings = [
        'currency'       => 'CHF',
        'priceChart'     => true,
        'feeChart'       => true,
        'darkTheme'      => false,
    ];

    expect(getSettingsFromCookies($this, 'all', $settings))->toBe($settings);
});

it('should have a currency setting', function () {
    expect(Settings::currency())->toBe('USD');

    expect(getSettingsFromCookies($this, 'currency', ['currency' => 'BTC']))
        ->toBe(['BTC']);
});

it('should have a price chart setting', function () {
    expect(Settings::priceChart())->toBeTrue();
});

it('should have a fee chart setting', function () {
    expect(Settings::feeChart())->toBeTrue();
});

it('should have a dark theme setting', function () {
    expect(Settings::theme())->toBeString();
});

it('should determine the name of the theme', function () {
    expect(Settings::theme())->toBe('auto');

    expect(getSettingsFromCookies($this, 'theme', ['darkTheme' => false]))
        ->toBe(['light']);

    expect(getSettingsFromCookies($this, 'theme', ['darkTheme' => true]))
        ->toBe(['dark']);
});

it('should determine if visitor uses any chart', function () {
    expect(getSettingsFromCookies($this, 'usesCharts', [
        'priceChart' => true,
        'feeChart'   => true,
    ]))->toBe([true]);

    expect(getSettingsFromCookies($this, 'usesCharts', [
        'priceChart' => false,
        'feeChart'   => false,
    ]))->toBe([false]);

    expect(getSettingsFromCookies($this, 'usesCharts', [
        'priceChart' => true,
        'feeChart'   => true,
    ]))->toBe([true]);
});

it('should determine if visitor uses price chart', function () {
    Config::set('arkscan.network', 'development');

    expect(Settings::usesPriceChart())->toBeFalse();

    Config::set('arkscan.network', 'production');

    expect(getSettingsFromCookies($this, 'usesPriceChart', [
        'priceChart' => true,
    ]))->toBe([true]);

    expect(getSettingsFromCookies($this, 'usesPriceChart', [
        'priceChart' => false,
    ]))->toBe([false]);
});

it('should determine if visitor uses fee chart', function () {
    expect(Settings::usesFeeChart())->toBeTrue();

    expect(getSettingsFromCookies($this, 'usesFeeChart', [
        'feeChart' => false,
    ]))->toBe([false]);

    expect(getSettingsFromCookies($this, 'usesFeeChart', [
        'feeChart' => true,
    ]))->toBe([true]);
});

it('should determine if visitor uses dark theme', function () {
    expect(getSettingsFromCookies($this, 'theme', [
        'darkTheme' => true,
    ]))->toBe(['dark']);

    expect(getSettingsFromCookies($this, 'theme', [
        'darkTheme' => false,
    ]))->toBe(['light']);
});

it('should get the locale', function () {
    expect(Settings::locale())->toBe('en_US');

    getSettingsFromCookies($this, 'currency', [
        'currency' => 'gbp',
    ]);

    expect(Settings::locale())->toBe('en_GB');
});

it('should get favorite delegates', function () {
    expect(Settings::favoriteDelegates())->toEqual(collect());

    getSettingsFromCookies($this, 'favoriteDelegates', [
        'favoriteDelegates' => [
            'test-public-key',
        ],
    ]);

    expect(Settings::favoriteDelegates())->toEqual(collect(['test-public-key']));
});

it('should determine if cookie has favorited delegates', function () {
    expect(Settings::favoriteDelegates())->toEqual(collect());

    getSettingsFromCookies($this, 'favoriteDelegates', [
        'favoriteDelegates' => [
            'test-public-key',
        ],
    ]);

    expect(Settings::hasFavoriteDelegate('test-public-key'))->toBeTrue();
});
