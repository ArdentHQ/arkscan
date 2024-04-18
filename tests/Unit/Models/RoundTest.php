<?php

declare(strict_types=1);

use App\Models\Round;
use App\Models\Wallet;

beforeEach(function () {
    $this->subject = Round::factory()->create();
});

it('should have a validator that forged the block', function () {
    foreach ($this->subject->validators as $publicKey) {
        Wallet::factory()->create(['public_key' => $publicKey]);
    }

    foreach ($this->subject->validators as $validator) {
        expect($validator)->toBeString();
    }
});
