<?php

declare(strict_types=1);

use App\Contracts\Network as NetworkContract;
use App\Models\MultiPayment;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Transactions\Aggregates\Historical\AveragesAggregate;
use Carbon\Carbon;
use Tests\Feature\Http\Livewire\__stubs\NetworkStub;

it('should return count for non-multipayment', function () {
    $daysSinceEpoch = 2;
    $networkStub    = new NetworkStub(true, Carbon::now()->subDay($daysSinceEpoch));

    app()->singleton(NetworkContract::class, fn () => $networkStub);

    expect((new AveragesAggregate())->aggregate())->toBe([
        'count'  => 0,
        'amount' => 0,
        'fee'    => 0,
    ]);

    $transactionCount = 12;

    Transaction::factory(6)
        ->transfer()
        ->create([
            'value'     => 20 * 1e18,
            'gas_price' => 25,
            'status'    => true,
        ]);

    Transaction::factory(6)
        ->unvote()
        ->create([
            'value'     => 0 * 1e18,
            'status'    => true,
            'gas_price' => 25,
        ]);

    expect(Transaction::count())->toBe($transactionCount);

    expect((new AveragesAggregate())->aggregate())->toBe([
        'count'  => $transactionCount / $daysSinceEpoch,
        'amount' => 120 / $daysSinceEpoch,
        'fee'    => (float) (((25 * 21000) * $transactionCount) / $daysSinceEpoch),
    ]);
});

it('should return count for multipayment', function () {
    $daysSinceEpoch = 2;
    $networkStub    = new NetworkStub(true, Carbon::now()->subDay($daysSinceEpoch));

    app()->singleton(NetworkContract::class, fn () => $networkStub);

    expect((new AveragesAggregate())->aggregate())->toBe([
        'count'  => 0,
        'amount' => 0,
        'fee'    => 0,
    ]);

    $transactionCount = 4;

    Transaction::factory(2)
        ->create([
            'value'     => 10 * 1e18,
            'gas_price' => 25,
            'status'    => true,
        ]);

    $recipient = Wallet::factory()->create();

    $multipaymentTransaction = Transaction::factory()
        ->multiPayment([$recipient->address], [BigNumber::new(14 * 1e18)])
        ->create([
            'value'     => 0,
            'status'    => true,
            'gas_price' => 25,
        ]);

    MultiPayment::factory()
        ->create([
            'to'     => $recipient->address,
            'from'   => $multipaymentTransaction->from,
            'hash'   => $multipaymentTransaction->hash,
            'amount' => BigNumber::new(14 * 1e18),
        ]);

    $multipaymentTransaction = Transaction::factory()
        ->multiPayment([$recipient->address, $recipient->address], [BigNumber::new(14 * 1e18), BigNumber::new(14 * 1e18)])
        ->create([
            'value'     => 0,
            'status'    => true,
            'gas_price' => 25,
        ]);

    MultiPayment::factory()
        ->count(2)
        ->create([
            'to'     => $recipient->address,
            'from'   => $multipaymentTransaction->from,
            'hash'   => $multipaymentTransaction->hash,
            'amount' => BigNumber::new(14 * 1e18),
        ]);

    expect(Transaction::count())->toBe($transactionCount);

    expect((new AveragesAggregate())->aggregate())->toBe([
        'count'  => (int) round($transactionCount / $daysSinceEpoch),
        'amount' => 62 / $daysSinceEpoch,
        'fee'    => (float) (((25 * 21000) * $transactionCount) / $daysSinceEpoch),
    ]);
});
