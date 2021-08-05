<?php

declare(strict_types=1);

use App\Models\Round;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

beforeEach(function () {
    $this->subject = Round::factory()->create(['balance' => '500000000000']);
});

it('should have a delegate that forged the block', function () {
    Wallet::factory()->create(['public_key' => $this->subject->public_key]);

    expect($this->subject->delegate())->toBeInstanceOf(BelongsTo::class);
    expect($this->subject->delegate)->toBeInstanceOf(Wallet::class);
});
