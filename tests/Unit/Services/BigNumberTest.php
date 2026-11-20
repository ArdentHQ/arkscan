<?php

declare(strict_types=1);

use App\Services\BigNumber;
use Brick\Math\RoundingMode;

it('should accept floats', function () {
    expect((string) BigNumber::new(1.5))->toBe('1.5');
    expect((string) BigNumber::new(1e18))->toBe('1000000000000000000');
    expect((string) BigNumber::new(1)->plus(1.5))->toBe('2.5');
    expect((string) BigNumber::new(2)->multipliedBy(1.5))->toBe('3.0');
    expect((string) BigNumber::new(3)->dividedBy(1.5))->toBe('2');
    expect(BigNumber::new(5e18)->toFloat(1e18))->toBe(5.0);
});

it('should divide using the scale of the current value by default', function () {
    expect((string) BigNumber::new('10.00')->dividedBy(3, roundingMode: RoundingMode::Down))->toBe('3.33');
    expect((string) BigNumber::new(10)->dividedBy(3, roundingMode: RoundingMode::Down))->toBe('3');
});
