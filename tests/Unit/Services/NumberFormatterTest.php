<?php

declare(strict_types=1);

use App\Services\NumberFormatter;

it('should format a number without a currency symbol', function () {
    expect(NumberFormatter::number(123456789))->toBe('123,456,789');
});

it('should format a number with a currency symbol', function () {
    expect(NumberFormatter::currency(123, 'ARK'))->toBe('ARK 123.00');
});

it('should format a number without a custom currency symbol', function () {
    expect(NumberFormatter::currencyWithSymbol(123, 'Ѧ'))->toBe('Ѧ 123');
});
