<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Wallet;
use App\Services\Search\BlockSearch;
use App\Services\Timestamp;
use Carbon\Carbon;

it('should search for a block by id', function (?string $modifier) {
    $block = Block::factory(10)->create()[0];

    $result = (new BlockSearch())->search([
        'term' => $modifier ? $modifier($block->id) : $block->id,
    ]);

    expect($result->get())->toHaveCount(1);
})->with([null, 'strtolower', 'strtoupper']);

it('should search for a block by generator public key', function (?string $modifier) {
    Block::factory(10)->create();

    $block = Block::factory()->create(['generator_public_key' => 'generator']);

    $result = (new BlockSearch())->search([
        'generatorPublicKey' => $modifier ? $modifier($block->generator_public_key) : $block->generator_public_key,
    ]);

    expect($result->get())->toHaveCount(1);
})->with([null, 'strtolower', 'strtoupper']);

it('should search for blocks by timestamp minimum', function () {
    $today        = Carbon::now();
    $todayGenesis = Timestamp::fromUnix($today->unix())->unix();

    $yesterday        = Carbon::now()->subDay();
    $yesterdayGenesis = Timestamp::fromUnix($yesterday->unix())->unix();

    Block::factory(10)->create(['timestamp' => $todayGenesis]);
    Block::factory(10)->create(['timestamp' => $yesterdayGenesis]);

    $result = (new BlockSearch())->search([
        'dateFrom' => $today->toString(),
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by timestamp maximum', function () {
    $today        = Carbon::now();
    $todayGenesis = Timestamp::fromUnix($today->unix())->unix();

    $yesterday        = Carbon::now()->subDay();
    $yesterdayGenesis = Timestamp::fromUnix($yesterday->unix())->unix();

    Block::factory(10)->create(['timestamp' => $todayGenesis]);
    Block::factory(10)->create(['timestamp' => $yesterdayGenesis]);

    $result = (new BlockSearch())->search([
        'dateTo' => $yesterday->toString(),
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by timestamp range', function () {
    $today        = Carbon::now();
    $todayGenesis = Timestamp::fromUnix($today->unix())->unix();

    $yesterday        = Carbon::now()->subDay();
    $yesterdayGenesis = Timestamp::fromUnix($yesterday->unix())->unix();

    Block::factory(10)->create(['timestamp' => $todayGenesis]);
    Block::factory(10)->create(['timestamp' => $yesterdayGenesis]);

    $result = (new BlockSearch())->search([
        'dateFrom' => $yesterday->toString(),
        'dateTo'   => $yesterday->toString(),
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by total_amount minimum', function () {
    Block::factory(10)->create(['total_amount' => 1000 * 1e8]);
    Block::factory(10)->create(['total_amount' => 2000 * 1e8]);

    $result = (new BlockSearch())->search([
        'totalAmountFrom' => 2000,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by total_amount maximum', function () {
    Block::factory(10)->create(['total_amount' => 1000 * 1e8]);
    Block::factory(10)->create(['total_amount' => 2000 * 1e8]);

    $result = (new BlockSearch())->search([
        'totalAmountTo' => 1000,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by total_amount range', function () {
    Block::factory(10)->create(['total_amount' => 1000 * 1e8]);
    Block::factory(10)->create(['total_amount' => 2000 * 1e8]);

    $result = (new BlockSearch())->search([
        'totalAmountFrom' => 500,
        'totalAmountTo'   => 1500,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by total_fee minimum', function () {
    Block::factory(10)->create(['total_fee' => 1000 * 1e8]);
    Block::factory(10)->create(['total_fee' => 2000 * 1e8]);

    $result = (new BlockSearch())->search([
        'totalFeeFrom' => 2000,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by total_fee maximum', function () {
    Block::factory(10)->create(['total_fee' => 1000 * 1e8]);
    Block::factory(10)->create(['total_fee' => 2000 * 1e8]);

    $result = (new BlockSearch())->search([
        'totalFeeTo' => 1000,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by total_fee range', function () {
    Block::factory(10)->create(['total_fee' => 1000 * 1e8]);
    Block::factory(10)->create(['total_fee' => 2000 * 1e8]);

    $result = (new BlockSearch())->search([
        'totalFeeFrom' => 500,
        'totalFeeTo'   => 1500,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by height minimum', function () {
    $heightStart = 1000;
    $heightEnd   = 2000;

    Block::factory(10)->create(['height' => $heightStart]);
    Block::factory(10)->create(['height' => $heightEnd]);

    $result = (new BlockSearch())->search([
        'heightFrom' => $heightEnd,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by height as term', function () {
    $height = 1234567;

    Block::factory()->create(['height' => $height]);

    $result = (new BlockSearch())->search([
        'term' => strval($height),
    ]);

    expect($result->get())->toHaveCount(1);
});

it('should search for blocks by formatted height as a term', function () {
    $height = 1234567;

    Block::factory()->create(['height' => $height]);

    $result = (new BlockSearch())->search([
        'term' => '1,234,567',
    ]);

    expect($result->get())->toHaveCount(1);
});

it('should search for blocks by height maximum', function () {
    $heightStart = 1000;
    $heightEnd   = 2000;

    Block::factory(10)->create(['height' => $heightStart]);
    Block::factory(10)->create(['height' => $heightEnd]);

    $result = (new BlockSearch())->search([
        'heightTo' => $heightStart,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by height range', function () {
    $heightStart = 1000;
    $heightEnd   = 2000;

    Block::factory(10)->create(['height' => $heightStart]);
    Block::factory(10)->create(['height' => $heightEnd]);

    $result = (new BlockSearch())->search([
        'heightFrom' => $heightEnd,
        'heightTo'   => $heightEnd,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by generator with an address', function (?string $modifier) {
    Block::factory(10)->create();

    $block = Block::factory()->create([
        'generator_public_key' => Wallet::factory()->create([
            'address'    => 'DUUT1TENLRT6qRDceBHGGcJtjU8kuQEmk4',
            'public_key' => '03b4d12354584371a54846082067c6da895dbe0699282dc462be0199a0f79b2d16',
        ])->public_key,
    ]);

    $result = (new BlockSearch())->search([
        'term' => $modifier ? $modifier($block->delegate->address) : $block->delegate->address,
    ]);

    expect($result->get())->toHaveCount(1);
})->with([null, 'strtolower', 'strtoupper']);

it('should search for blocks by generator with a public key', function (?string $modifier) {
    Block::factory(10)->create();

    $block = Block::factory()->create([
        'generator_public_key' => Wallet::factory()->create([
            'public_key' => '03b4d12354584371a54846082067c6da895dbe0699282dc462be0199a0f79b2d16',
        ])->public_key,
    ]);

    $result = (new BlockSearch())->search([
        'term' => $modifier ? $modifier($block->delegate->public_key) : $block->delegate->public_key,
    ]);

    expect($result->get())->toHaveCount(1);
})->with([null, 'strtolower', 'strtoupper']);

it('should search for blocks by generator with a username', function (?string $modifier) {
    Block::factory(10)->create();

    $block = Block::factory()->create([
        'generator_public_key' => Wallet::factory()->create([
            'attributes' => [
                'delegate' => [
                    'username' => 'johndoe',
                ],
            ],
        ])->public_key,
    ]);

    $result = (new BlockSearch())->search([
        'term' => $modifier ? $modifier($block->delegate->attributes['delegate']['username']) : $block->delegate->attributes['delegate']['username'],
    ]);

    expect($result->get())->toHaveCount(1);
})->with([null, 'strtolower', 'strtoupper']);

it('should search for blocks by generator with a username containing special characters', function (?string $modifier) {
    Block::factory(10)->create();

    $block = Block::factory()->create([
        'generator_public_key' => Wallet::factory()->create([
            'attributes' => [
                'delegate' => [
                    'username' => 'john.doe (old) [new] 2',
                ],
            ],
        ])->public_key,
    ]);

    $result = (new BlockSearch())->search([
        'term' => $modifier ? $modifier($block->delegate->attributes['delegate']['username']) : $block->delegate->attributes['delegate']['username'],
    ]);

    expect($result->get())->toHaveCount(1);
})->with([null, 'strtolower', 'strtoupper']);
