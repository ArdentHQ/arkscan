<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use function Tests\configureExplorerDatabase;

beforeEach(function () {
    configureExplorerDatabase();

    $this->subject = Transaction::factory()->create([
        'fee'    => 1 * 1e8,
        'amount' => 2 * 1e8,
    ]);
});

it('should belong to a block', function () {
    expect($this->subject->block())->toBeInstanceOf(BelongsTo::class);
    expect($this->subject->block)->toBeInstanceOf(Block::class);
});

it('should belong to a sender', function () {
    Wallet::factory()->create(['public_key' => $this->subject->sender_public_key]);

    expect($this->subject->sender())->toBeInstanceOf(BelongsTo::class);
    expect($this->subject->sender)->toBeInstanceOf(Wallet::class);
});

it('should belong to a recipient', function () {
    Wallet::factory()->create(['address' => $this->subject->recipient_id]);

    expect($this->subject->recipient())->toBeInstanceOf(BelongsTo::class);
    expect($this->subject->recipient)->toBeInstanceOf(Wallet::class);
});

it('should order transactions by their timestamp from new to old', function () {
    expect($this->subject->latestByTimestamp())->toBeInstanceOf(Builder::class);
});

it('should only query transactions that were sent by the given public key', function () {
    expect($this->subject->sendBy('some-public-key'))->toBeInstanceOf(Builder::class);
});

it('should only query transactions that were received by the given address', function () {
    expect($this->subject->receivedBy('some-address'))->toBeInstanceOf(Builder::class);
});

it('should get the timestamp as a Carbon instance', function () {
    expect($this->subject->timestamp_carbon)->toBeInstanceOf(Carbon::class);
    expect((string) $this->subject->timestamp_carbon)->toBe('2020-10-19 04:54:16');
});

it('should get the vendor field', function () {
    expect($this->subject->vendor_field)->toBeNull();

    $this->subject->update([
        'vendor_field_hex' => hex2bin('Hello World'),
    ]);

    expect($this->subject->vendor_field)->toBeString();
    expect($this->subject->vendor_field)->toBe('Hello World');
})->skip();

it('should get the formatted fee', function () {
    expect($this->subject->formatted_fee)->toBeString();
    expect($this->subject->formatted_fee)->toBe('ARK 1.00');
});

it('should get the formatted amount', function () {
    expect($this->subject->formatted_amount)->toBeString();
    expect($this->subject->formatted_amount)->toBe('ARK 2.00');
});
