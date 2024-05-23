<?php

declare(strict_types=1);

use App\DTO\Statistics\AddressHoldingStatistics;

it('should convert to and from wireable array', function () {
    $subject = AddressHoldingStatistics::make([
        [
            'count'   => 2,
            'grouped' => 1,
        ],
        [
            'count'   => 2000,
            'grouped' => 1000,
        ],
        [
            'count'   => 20000,
            'grouped' => 10000,
        ],
        [
            'count'   => 200000,
            'grouped' => 100000,
        ],
        [
            'count'   => 2000000,
            'grouped' => 1000000,
        ],
    ]);

    expect($subject->toLivewire())->toBe([
        1       => 2,
        1000    => 2000,
        10000   => 20000,
        100000  => 200000,
        1000000 => 2000000,
    ]);

    $subject = AddressHoldingStatistics::fromLivewire($subject->toLivewire());

    expect($subject->greaterThanOne)->toBe(2);
    expect($subject->greaterThanOneThousand)->toBe(2000);
    expect($subject->greaterThanTenThousand)->toBe(20000);
    expect($subject->greaterThanOneHundredThousand)->toBe(200000);
    expect($subject->greaterThanOneMillion)->toBe(2000000);
});
