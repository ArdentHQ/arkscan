<?php

declare(strict_types=1);

use App\Services\NumberFormatter;

use function Spatie\Snapshots\assertMatchesSnapshot;

it('should format a number without a currency symbol', function () {
    assertMatchesSnapshot(NumberFormatter::number(123456789));
});

it('should format a number with a currency symbol', function () {
    assertMatchesSnapshot(NumberFormatter::currency(123, 'ARK'));
});
