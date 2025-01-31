<?php

declare(strict_types=1);

use App\Services\Cache\ContractCache;

beforeEach(fn () => $this->subject = new ContractCache());

it('should get and set token name', function () {
    expect($this->subject->getTokenName('address'))->toBeNull();

    $this->subject->setTokenName('address', 'test-name');

    expect($this->subject->getTokenName('address'))->toBe('test-name');
});

it('should determine if cache has token name', function () {
    expect($this->subject->hasTokenName('address'))->toBeFalse();

    $this->subject->setTokenName('address', 'test-name');

    expect($this->subject->hasTokenName('address'))->toBeTrue();
});
