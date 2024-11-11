<?php

declare(strict_types=1);

use App\Contracts\Network as NetworkContract;
use App\Models\Transaction;
use App\Services\Transactions\Aggregates\Historical\AveragesAggregate;
use Carbon\Carbon;
use Tests\Feature\Http\Livewire\__stubs\NetworkStub;

it('should return count', function () {
    $daysSinceEpoch = 2;
    $networkStub = new NetworkStub(true, Carbon::now()->subDay($daysSinceEpoch));

    app()->singleton(NetworkContract::class, fn () => $networkStub);

    expect((new AveragesAggregate())->aggregate())->toBe([
        'count'  => 0,
        'amount' => 0,
        'fee'    => 0,
    ]);

    $transactionCount = 12;

    Transaction::factory($transactionCount)
        ->withReceipt()
        ->create([
            'amount'    => 10 * 1e18,
            'gas_price' => 25,
        ]);

    expect((new AveragesAggregate())->aggregate())->toBe([
        'count'  => $transactionCount / $daysSinceEpoch,
        'amount' => 60,
        'fee'    => (((25 * 21000) * $transactionCount) / $daysSinceEpoch) / 1e9,
    ]);
});
