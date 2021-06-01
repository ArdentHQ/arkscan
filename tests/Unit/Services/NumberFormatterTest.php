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

it('should format a number with a currency symbol if it has a [.]', function () {
    assertMatchesSnapshot(NumberFormatter::currency(123.456, 'ARK'));
    assertMatchesSnapshot(NumberFormatter::currency('0.000003', 'ARK'));
});

it('should format a number without a suffix', function () {
    assertMatchesSnapshot(NumberFormatter::currencyWithoutSuffix(123, 'ARK'));
    assertMatchesSnapshot(NumberFormatter::currencyWithoutSuffix(1e8, 'ARK'));
});

it('should format a number with a currency symbol if it has a [,]', function () {
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
