<?php

declare(strict_types=1);

use App\Models\Round;
use App\Models\Wallet;
use App\Services\Monitor\Monitor;

use function Tests\configureExplorerDatabase;

it('should get the active delegates for the given round', function () {
    configureExplorerDatabase();

    Wallet::factory(51)->create()->each(function ($wallet) {
        Round::factory()->create([
            'round'      => '112168',
            'public_key' => $wallet->public_key,
        ]);
    });

    expect(Monitor::activeDelegates(112168))->toHaveCount(51);
})->skip();

it('should calculate the forging information', function () {
    expect(Monitor::roundNumber())->toBeInt();
});
