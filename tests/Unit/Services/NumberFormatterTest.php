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
});

it('should format a number with an ordinal', function () {
    expect(NumberFormatter::ordinal(1))->toBe('1st');
    expect(NumberFormatter::ordinal(2))->toBe('2nd');
    expect(NumberFormatter::ordinal(3))->toBe('3rd');
});
