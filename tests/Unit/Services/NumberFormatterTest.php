<?php

declare(strict_types=1);

use App\Services\NumberFormatter;
use function Spatie\Snapshots\assertMatchesSnapshot;

it('should format a number without a currency symbol', function () {
    assertMatchesSnapshot(NumberFormatter::number(123456789));
});

it('should format a number with a percentage symbol', function () {
    assertMatchesSnapshot(NumberFormatter::percentage(10, '10%'));
});

it('should format a number without a currency symbol from a satoshi value', function () {
    assertMatchesSnapshot(NumberFormatter::satoshi('12300000000', 'ARK'));
});

it('should format a number with a currency symbol', function () {
    assertMatchesSnapshot(NumberFormatter::currency(123, 'ARK'));
    assertMatchesSnapshot(NumberFormatter::currency(1e8, 'ARK'));
});

it('should format a number with a currency symbol if it has a decimal', function () {
    assertMatchesSnapshot(NumberFormatter::currency(123.456, 'ARK'));
    assertMatchesSnapshot(NumberFormatter::currency('0.000003', 'ARK'));
});

it('should format fiat currency with the correct decimal places', function () {
    assertMatchesSnapshot(NumberFormatter::currency(123.456, 'GBP'));
    assertMatchesSnapshot(NumberFormatter::currency('123.456', 'GBP'));

    assertMatchesSnapshot(NumberFormatter::currency(123.456, 'USD'));
    assertMatchesSnapshot(NumberFormatter::currency('123.456', 'USD'));

    assertMatchesSnapshot(NumberFormatter::currency(123.456, 'CAD'));
    assertMatchesSnapshot(NumberFormatter::currency('123.456', 'CAD'));
});

it('should format crypto currency with the correct decimal places', function () {
    assertMatchesSnapshot(NumberFormatter::currency(123.456, 'BTC'));
    assertMatchesSnapshot(NumberFormatter::currency('123.456', 'BTC'));
});

it('should format a number without a suffix', function () {
    assertMatchesSnapshot(NumberFormatter::currencyWithoutSuffix(123, 'ARK'));
    assertMatchesSnapshot(NumberFormatter::currencyWithoutSuffix(1e8, 'ARK'));
});

it('should format a number with decimals and without a suffix', function () {
    assertMatchesSnapshot(NumberFormatter::currencyWithDecimalsWithoutSuffix(1.00, 'USD'));
    assertMatchesSnapshot(NumberFormatter::currencyWithDecimalsWithoutSuffix(1.65, 'USD'));
    assertMatchesSnapshot(NumberFormatter::currencyWithDecimalsWithoutSuffix(1.001, 'USD'));
});

it('should format a number with a currency symbol if it has a comma', function () {
    assertMatchesSnapshot(NumberFormatter::currency('123,456', 'ARK'));
});

it('should format a number with a currency short notation if it is too large', function () {
    assertMatchesSnapshot(NumberFormatter::currencyShort(1000, 'ARK'));
    assertMatchesSnapshot(NumberFormatter::currencyShort(10000, 'ARK'));
    assertMatchesSnapshot(NumberFormatter::currencyShort(100000, 'ARK'));
    assertMatchesSnapshot(NumberFormatter::currencyShort(1000000, 'ARK'));
    assertMatchesSnapshot(NumberFormatter::currencyShort(10000000, 'ARK'));
    assertMatchesSnapshot(NumberFormatter::currencyShort(100000000, 'ARK'));
    assertMatchesSnapshot(NumberFormatter::currencyShort(1000000000, 'ARK'));
    assertMatchesSnapshot(NumberFormatter::currencyShort(10000000000, 'ARK'));
    assertMatchesSnapshot(NumberFormatter::currencyShort(100000000000, 'ARK'));
    assertMatchesSnapshot(NumberFormatter::currencyShort(1000000000000, 'ARK'));
    assertMatchesSnapshot(NumberFormatter::currencyShort(10000000000000, 'ARK'));
    assertMatchesSnapshot(NumberFormatter::currencyShort(100000000000000, 'ARK'));
});

it('should format a number with a K notation', function () {
    assertMatchesSnapshot(NumberFormatter::currencyShortNotation(999));
    assertMatchesSnapshot(NumberFormatter::currencyShortNotation(1000));
    assertMatchesSnapshot(NumberFormatter::currencyShortNotation(10000));
    assertMatchesSnapshot(NumberFormatter::currencyShortNotation(100000));
    assertMatchesSnapshot(NumberFormatter::currencyShortNotation(105999));
    assertMatchesSnapshot(NumberFormatter::currencyShortNotation(1000000));
    assertMatchesSnapshot(NumberFormatter::currencyShortNotation(10000000));
    assertMatchesSnapshot(NumberFormatter::currencyShortNotation(100000000));
    assertMatchesSnapshot(NumberFormatter::currencyShortNotation(104910000));
});

it('should use two decimals for a fiat currency', function () {
    expect(NumberFormatter::decimalsFor('USD'))->toBe(2);
    expect(NumberFormatter::decimalsFor('BTC'))->toBe(8);
    expect(NumberFormatter::decimalsFor('ETH'))->toBe(8);
    expect(NumberFormatter::decimalsFor('LTC'))->toBe(8);
});

it('should use up to decimals and trim zero', function () {
    expect(NumberFormatter::currencyWithDecimals(1, 'USD'))->toBe('$1.00');
    expect(NumberFormatter::currencyWithDecimals(1.1, 'USD'))->toBe('$1.10');
    expect(NumberFormatter::currencyWithDecimals(1.5, 'USD'))->toBe('$1.50');
    expect(NumberFormatter::currencyWithDecimals(1.05, 'USD'))->toBe('$1.05');
    expect(NumberFormatter::currencyWithDecimals(1.005, 'USD'))->toBe('$1.005');
    expect(NumberFormatter::currencyWithDecimals(1.0005, 'USD'))->toBe('$1.0005');
    expect(NumberFormatter::currencyWithDecimals(1.00005, 'USD'))->toBe('$1.0001');
    expect(NumberFormatter::currencyWithDecimals(1.00004, 'USD'))->toBe('$1.00');
    expect(NumberFormatter::currencyWithDecimals(1.00006, 'USD'))->toBe('$1.0001');
    expect(NumberFormatter::currencyWithDecimals(1.00000, 'USD'))->toBe('$1.00');
    expect(NumberFormatter::currencyWithDecimals(1.1200001, 'USD'))->toBe('$1.12');
    expect(NumberFormatter::currencyWithDecimals(1.1200001, 'USD', 1))->toBe('$1.10');
    expect(NumberFormatter::currencyWithDecimals(29510, 'USD'))->toBe('$29,510.00');
    expect(NumberFormatter::currencyWithDecimals(29510.1, 'USD'))->toBe('$29,510.10');
    expect(NumberFormatter::currencyWithDecimals(125000000.000000, 'USD'))->toBe('$125,000,000.00');
});

it('should format other currencies', function ($currency, $expectation) {
    expect(NumberFormatter::currencyWithDecimals(1.0005, $currency))->toBe($expectation);
})->with([
    'GBP' => ['GBP', '£1.0005'],
    'EUR' => ['EUR', '€1.0005'],
    'ETH' => ['ETH', '1.0005 ETH'],
    'BTC' => ['BTC', '1.0005 BTC'],
]);

it('should properly trim zeros for non fiat values', function ($value, $expected) {
    expect(NumberFormatter::currencyWithDecimals($value, 'ARK', 0))->toBe($expected);
})->with([
    [125000000.000, '125,000,000 ARK'],
    [125000000.1234, '125,000,000 ARK'],
    [125000001.1234, '125,000,001 ARK'],
    [125000000.000123, '125,000,000 ARK'],
]);

it('should format with network currency', function () {
    expect(NumberFormatter::networkCurrency(1.0005))->toBe('1.0005');
});

it('should format with network currency and suffix', function () {
    expect(NumberFormatter::networkCurrency(1.0005, 8, true))->toBe('1.0005 DARK');
});

it('should format with network currency with different decimal places', function () {
    expect(NumberFormatter::networkCurrency(1.0005, 2, true))->toBe('1.00 DARK');
    expect(NumberFormatter::networkCurrency(1.0095, 2, true))->toBe('1.01 DARK');
});

it('should determine that currency has a symbol', function ($currency) {
    expect(NumberFormatter::hasSymbol($currency))->toBeTrue();
})->with([
    'AUD',
    'BRL',
    'CAD',
    'CNY',
    'EUR',
    'GBP',
    'JPY',
    'KRW',
    'NZD',
    'RUB',
    'USD',
]);

it('should determine that currency does not have a symbol', function ($currency) {
    expect(NumberFormatter::hasSymbol($currency))->toBeFalse();
})->with([
    'BTC',
    'CHF',
    'ETH',
    'LTC',
]);

it('should format values for views', function ($currency, $expectation) {
    expect(NumberFormatter::currencyForViews(1.0005, $currency))->toBe($expectation);
})->with([
    'GBP' => ['GBP', '£1'],
    'EUR' => ['EUR', '€1'],
    'ETH' => ['ETH', '1.0005 ETH'],
    'BTC' => ['BTC', '1.0005 BTC'],
]);
