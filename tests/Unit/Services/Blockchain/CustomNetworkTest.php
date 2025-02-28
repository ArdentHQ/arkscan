<?php

declare(strict_types=1);

use App\Services\Blockchain\CustomNetwork;

it('should have a wif, epoch and chain ID', function () {
    $subject = new CustomNetwork(['base58Prefix' => 23, 'epoch' => '2017-03-21T13:00:00.000Z']);

    expect($subject->getAddressByte())->toBe('17');
    expect($subject->epoch())->toBe('2017-03-21T13:00:00.000Z');
    expect($subject->chainId())->toBe(10000);
});
