<?php

declare(strict_types=1);

use App\Models\Block;
use App\Services\Search\BlockSearch;

use App\Services\Timestamp;
use Carbon\Carbon;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should search for a block by id', function () {
    $block = Block::factory(10)->create()[0];

    $result = (new BlockSearch())->search([
        'term' => $block->id,
    ]);

    expect($result->get())->toHaveCount(1);
});

it('should search for a block by generator public key', function () {
    $block = Block::factory(10)->create()[0];

    $result = (new BlockSearch())->search([
        'generatorPublicKey' => $block->generator_public_key,
    ]);

    expect($result->get())->toHaveCount(1);
});

it('should search for blocks by timestamp minimum', function () {
    $today = Carbon::now();
    $todayGenesis = Timestamp::fromUnix($today->unix())->unix();

    $yesterday = Carbon::now()->subDay();
    $yesterdayGenesis = Timestamp::fromUnix($yesterday->unix())->unix();

    Block::factory(10)->create(['timestamp' => $todayGenesis]);
    Block::factory(10)->create(['timestamp' => $yesterdayGenesis]);

    $result = (new BlockSearch())->search([
        'dateFrom' => $today->toString(),
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by timestamp maximum', function () {
    $today = Carbon::now();
    $todayGenesis = Timestamp::fromUnix($today->unix())->unix();

    $yesterday = Carbon::now()->subDay();
    $yesterdayGenesis = Timestamp::fromUnix($yesterday->unix())->unix();

    Block::factory(10)->create(['timestamp' => $todayGenesis]);
    Block::factory(10)->create(['timestamp' => $yesterdayGenesis]);

    $result = (new BlockSearch())->search([
        'dateTo' => $yesterday->toString(),
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by timestamp range', function () {
    $today = Carbon::now();
    $todayGenesis = Timestamp::fromUnix($today->unix())->unix();

    $yesterday = Carbon::now()->subDay();
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
    Block::factory(10)->create(['total_amount' => 1000]);
    Block::factory(10)->create(['total_amount' => 2000]);

    $result = (new BlockSearch())->search([
        'totalAmountFrom' => 2000,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by total_amount maximum', function () {
    Block::factory(10)->create(['total_amount' => 1000]);
    Block::factory(10)->create(['total_amount' => 2000]);

    $result = (new BlockSearch())->search([
        'totalAmountTo' => 1000,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by total_amount range', function () {
    Block::factory(10)->create(['total_amount' => 1000]);
    Block::factory(10)->create(['total_amount' => 2000]);

    $result = (new BlockSearch())->search([
        'totalAmountFrom' => 500,
        'totalAmountTo'   => 1500,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by total_fee minimum', function () {
    Block::factory(10)->create(['total_fee' => 1000]);
    Block::factory(10)->create(['total_fee' => 2000]);

    $result = (new BlockSearch())->search([
        'totalFeeFrom' => 2000,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by total_fee maximum', function () {
    Block::factory(10)->create(['total_fee' => 1000]);
    Block::factory(10)->create(['total_fee' => 2000]);

    $result = (new BlockSearch())->search([
        'totalFeeTo' => 1000,
    ]);

    expect($result->get())->toHaveCount(10);
});

it('should search for blocks by total_fee range', function () {
    Block::factory(10)->create(['total_fee' => 1000]);
    Block::factory(10)->create(['total_fee' => 2000]);

    $result = (new BlockSearch())->search([
        'totalFeeFrom' => 500,
        'totalFeeTo'   => 1500,
    ]);

    expect($result->get())->toHaveCount(10);
});
