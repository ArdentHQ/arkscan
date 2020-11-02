<?php

declare(strict_types=1);

use App\Services\Cache\TableCache;
use Illuminate\Support\Collection;

beforeEach(fn () => $this->subject = new TableCache());

it('should get and set the latest blocks', function () {
    expect($this->subject->setLatestBlocks(fn () => collect([])))->toBeInstanceOf(Collection::class);
});

it('should get and set the latest transactions', function () {
    expect($this->subject->setLatestTransactions('type', fn () => collect([])))->toBeInstanceOf(Collection::class);
});
