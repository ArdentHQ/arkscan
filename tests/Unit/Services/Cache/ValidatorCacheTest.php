<?php

declare(strict_types=1);

use App\Services\Cache\ValidatorCache;
use Illuminate\Support\Facades\Cache;

beforeEach(fn () => $this->subject = new ValidatorCache());

it('should get and set the latest blocks', function () {
    Cache::tags('validator')->put(md5('total_wallets_voted'), '10');

    expect($this->subject->getTotalWalletsVoted())->toEqual(10);
});

it('should get and set the latest transactions', function () {
    Cache::tags('validator')->put(md5('total_balance_voted'), '10.0');

    expect($this->subject->getTotalBalanceVoted())->toEqual(10.0);
});
