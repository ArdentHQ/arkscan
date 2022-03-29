<?php

declare(strict_types=1);

use App\Facades\Rounds;
use App\Http\Livewire\DelegateMonitor;
use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use App\Services\Monitor\DelegateTracker;
use App\Services\Monitor\ForgingInfoCalculator;
use App\Services\Monitor\Slots;
use Carbon\Carbon;
use Livewire\Livewire;

beforeEach(function () {
    $this->activeDelegates = require dirname(dirname(dirname(__DIR__))).'/fixtures/forgers.php';
});

function createRoundWithDelegates(): void
{
    Wallet::factory(51)->create()->each(function ($wallet) {
        $block = Block::factory()->create([
            'height'               => 5720529,
            'timestamp'            => 113620904,
            'generator_public_key' => $wallet->public_key,
        ]);

        // Start height for round 112168
        Block::factory()->create([
            'height'               => 5720518,
            'timestamp'            => 113620904,
            'generator_public_key' => $wallet->public_key,
        ]);

        Round::factory()->create([
            'round'      => '112168',
            'public_key' => $wallet->public_key,
        ]);

        (new WalletCache())->setDelegate($wallet->public_key, $wallet);

        (new WalletCache())->setLastBlock($wallet->public_key, [
            'id'     => $block->id,
            'height' => $block->height->toNumber(),
        ]);
    });
}

function forgeBlock(string $publicKey, int &$height): void
{
    $block = Block::factory()->create([
        'height'               => $height,
        'generator_public_key' => $publicKey,
        'timestamp'            => (new Slots())->getTime(),
    ]);

    (new WalletCache())->setLastBlock($publicKey, [
        'id'     => $block->id,
        'height' => $block->height->toNumber(),
    ]);

    $height++;
}

// @TODO: make assertions about data visibility
it('should render without errors', function () {
    createRoundWithDelegates();

    $component = Livewire::test(DelegateMonitor::class);
    $component->call('pollDelegates');
});

it('should get the last blocks from the last 2 rounds and beyond', function () {
    $wallets = Wallet::factory(51)->create()->each(function ($wallet) {
        Round::factory()->create([
            'round'      => '1',
            'public_key' => $wallet->public_key,
        ]);

        for ($i = 0; $i < 3; $i++) {
            Block::factory()->create([
                'height'               => $i,
                'generator_public_key' => $wallet->public_key,
            ]);
        }

        (new WalletCache())->setDelegate($wallet->public_key, $wallet);
    });

    $wallets->first()->blocks()->delete();

    Livewire::test(DelegateMonitor::class)->call('pollDelegates');

    expect((new WalletCache())->getLastBlock($wallets->first()->public_key))->toBe([]);

    foreach ($wallets->skip(1) as $wallet) {
        expect((new WalletCache())->getLastBlock($wallet->public_key))->not()->toBe([]);
    }
});

it('should correctly show the block is missed', function () {
    // Force round time
    $this->travelTo(new Carbon('2021-01-01 00:04:00'));

    // Create wallets for each delegate
    $this->activeDelegates->each(function ($delegate) use (&$wallets) {
        $wallet = Wallet::factory()->create(['public_key' => $delegate->public_key]);

        Round::factory()->create([
            'round'      => '1',
            'public_key' => $delegate->public_key,
            'balance'    => 0,
        ]);

        (new WalletCache())->setDelegate($delegate->public_key, $wallet);
    });

    // Store delegate record for each Round object
    $wallets = Rounds::allByRound(1)->map(fn ($round) => $round->delegate);

    // Make methods public for fetching forging order
    $activeDelegatesMethod  = new ReflectionMethod(DelegateTracker::class, 'getActiveDelegates');
    $shuffleDelegatesMethod = new ReflectionMethod(DelegateTracker::class, 'shuffleDelegates');
    $orderDelegatesMethod   = new ReflectionMethod(DelegateTracker::class, 'orderDelegates');
    $activeDelegatesMethod->setAccessible(true);
    $shuffleDelegatesMethod->setAccessible(true);
    $orderDelegatesMethod->setAccessible(true);

    // Get delegate order so we can forge in the correct order
    $originalOrder     = ForgingInfoCalculator::calculate((new Slots())->getTime(), 1);
    $activeDelegates   = $activeDelegatesMethod->invokeArgs(null, [$wallets]);
    $shuffledDelegates = $shuffleDelegatesMethod->invokeArgs(null, [$activeDelegates, 1]);
    $delegatesInOrder  = collect($orderDelegatesMethod->invokeArgs(null, [
        $shuffledDelegates,
        $originalOrder['currentForger'],
        51,
    ]));

    // Forge blocks for first 5 delegates
    $height = 1;
    $delegatesInOrder->take(5)->each(function ($publicKey) use (&$height) {
        forgeBlock($publicKey, $height);

        $this->travel(8)->seconds();
    });

    // Mark component delegate property as public & update monitor data
    $delegateProperty = new ReflectionProperty(DelegateMonitor::class, 'delegates');
    $delegateProperty->setAccessible(true);

    $component = Livewire::test(DelegateMonitor::class);
    $instance  = $component->instance();
    $instance->pollDelegates();

    $delegates = collect($delegateProperty->getValue($instance));

    expect($delegates)->toHaveCount(51);

    // Split up delegate slot data to check
    $forgedDelegates  = $delegates->splice(0, 5);
    $waitingDelegates = $delegates->splice(0, 1);
    $missedDelegates  = $delegates->splice(0, 5);

    $forgedDelegates->each(fn ($delegate) => expect($delegate->hasForged())->toBeTrue());
    $waitingDelegates->each(fn ($delegate) => expect($delegate->isNext())->toBeTrue());
    $missedDelegates->each(fn ($delegate) => expect($delegate->isPending())->toBeTrue());

    // Progress time by 15 delegate slots
    $this->travel(14 * 8)->seconds();

    // Forge block with 20th delegate
    forgeBlock($delegatesInOrder->get(20), $height);
    $this->travel(8)->seconds();

    // Update delegate data again
    $instance->pollDelegates();

    $delegates = collect($delegateProperty->getValue($instance));

    expect($delegates)->toHaveCount(51);

    // Check delegate data is correct after 15 missed blocks
    $forgedDelegates  = $delegates->splice(0, 5);
    $missedDelegates  = $delegates->splice(0, 15);
    $waitingDelegates = $delegates->splice(0, 1);

    $forgedDelegates->each(fn ($delegate) => expect($delegate->isWaiting())->toBeFalse());
    $forgedDelegates->each(fn ($delegate) => expect($delegate->hasForged())->toBeTrue());
    $missedDelegates->each(fn ($delegate) => expect($delegate->isWaiting())->toBeFalse());
    $missedDelegates->each(fn ($delegate) => expect($delegate->justMissed())->toBeTrue());
    $waitingDelegates->each(fn ($delegate) => expect($delegate->isNext())->toBeTrue());

    $outputData = [];
    $forgedDelegates->each(function ($delegate) use (&$outputData) {
        $outputData[] = $delegate->wallet()->username();
        $outputData[] = 'Completed';
    });
    $missedDelegates->each(function ($delegate) use (&$outputData) {
        $outputData[] = $delegate->wallet()->username();
        $outputData[] = 'Missed';
    });
    $waitingDelegates->each(function ($delegate) use (&$outputData) {
        $outputData[] = $delegate->wallet()->username();
        $outputData[] = 'Now';
    });

    $component
        ->call('pollDelegates')
        ->assertSeeInOrder($outputData);
});
