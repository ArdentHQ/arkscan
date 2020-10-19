<?php

declare(strict_types=1);

use App\Models\Round;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use function Tests\configureExplorerDatabase;

beforeEach(function () {
    configureExplorerDatabase();

    $this->subject = Round::factory()->create(['balance' => 5000 * 1e8]);
});

it('should have a delegate that forged the block', function () {
    Wallet::factory()->create(['public_key' => $this->subject->public_key]);

    expect($this->subject->delegate())->toBeInstanceOf(BelongsTo::class);
    expect($this->subject->delegate)->toBeInstanceOf(Wallet::class);
});

it('should have transactions', function () {
    expect($this->subject->formatted_balance)->toBe(5000.0);
});
