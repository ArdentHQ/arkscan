<?php

declare(strict_types=1);

use App\Contracts\Network as NetworkContract;
use App\Models\Transaction;
use App\Services\Transactions\Aggregates\Historical\AveragesAggregate;
use Carbon\Carbon;
use Tests\Feature\Http\Livewire\__stubs\NetworkStub;

it('should return count', function () {
    $networkStub = new NetworkStub(true, Carbon::now()->subDay(2));
    app()->singleton(NetworkContract::class, fn () => $networkStub);

    expect((new AveragesAggregate())->aggregate())->toBe([
        'count' => 0,
        'amount' => 0,
        'fee' => 0,
    ]);

    Transaction::factory(12)->delegateRegistration()->create([
        'amount' => 0,
        'fee' => 25 * 1e8,
    ]);

    expect((new AveragesAggregate())->aggregate())->toBe([
        'count' => 12 / 2,
        'amount' => 0,
        'fee' => (25 * 12) / 2,
    ]);
});
