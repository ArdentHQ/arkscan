<?php

declare(strict_types=1);

use App\Contracts\Network as NetworkContract;
use App\Enums\StatsPeriods;
use App\Enums\StatsTransactionType;
use App\Events\Statistics\TransactionDetails;
use App\Models\Transaction;
use App\Services\Cache\TransactionCache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Tests\Feature\Http\Livewire\__stubs\NetworkStub;

it('should cache data', function (): void {
    Event::fake();

    $this->travelTo(Carbon::parse('2024-04-08 15:22:19'));

    $networkStub = new NetworkStub(true, Carbon::now()->subDay(2));
    app()->singleton(NetworkContract::class, fn () => $networkStub);

    $cache = new TransactionCache();

    Transaction::factory(2)->delegateRegistration()->create([
        'amount' => 0,
        'fee'    => 9 * 1e8,
    ]);
    Transaction::factory(3)->transfer()->create([
        'amount' => 2000 * 1e8,
        'fee'    => 10 * 1e8,
    ]);
    Transaction::factory(4)->multipayment()->create([
        'amount' => 0,
        'fee'    => 11 * 1e8,
        'asset'  => [
            'payments' => [
                [
                    'amount' => 3000 * 1e8,
                ],
            ],
        ],
    ]);
    $largestTransaction = Transaction::factory()
        ->transfer()
        ->create([
            'amount' => 9000 * 1e8,
            'fee'    => 10 * 1e8,
        ]);

    expect(Transaction::count())->toBe(10);

    $transactionCount = (int) round(10 / 2);
    $totalAmount      = (int) round(((4 * 3000) + (3 * 2000) + 9000) / 2);
    $totalFees        = (int) round(((9 * 2) + (10 * 3) + (11 * 4) + 10) / 2);

    Artisan::call('explorer:cache-transactions');

    expect($cache->getHistoricalByType(StatsTransactionType::DELEGATE_REGISTRATION))->toBe(2);
    expect($cache->getHistoricalByType(StatsTransactionType::TRANSFER))->toBe(4);
    expect($cache->getHistoricalByType(StatsTransactionType::MULTIPAYMENT))->toBe(4);
    expect($cache->getHistoricalByType(StatsTransactionType::DELEGATE_RESIGNATION))->toBe(0);
    expect($cache->getHistoricalByType(StatsTransactionType::SWITCH_VOTE))->toBe(0);
    expect($cache->getHistoricalByType(StatsTransactionType::UNVOTE))->toBe(0);
    expect($cache->getHistoricalByType(StatsTransactionType::VOTE))->toBe(0);

    expect($cache->getHistorical(StatsPeriods::DAY))->toEqual([
        'labels' => [
            0  => '16',
            1  => '17',
            2  => '18',
            3  => '19',
            4  => '20',
            5  => '21',
            6  => '22',
            7  => '23',
            8  => '00',
            9  => '01',
            10 => '02',
            11 => '03',
            12 => '04',
            13 => '05',
            14 => '06',
            15 => '07',
            16 => '08',
            17 => '09',
            18 => '10',
            19 => '11',
            20 => '12',
            21 => '13',
            22 => '14',
            23 => '15',
        ],
        'datasets' => [
            0  => 0,
            1  => 0,
            2  => 0,
            3  => 0,
            4  => 0,
            5  => 0,
            6  => 0,
            7  => 0,
            8  => 0,
            9  => 0,
            10 => 0,
            11 => 0,
            12 => 0,
            13 => 0,
            14 => 0,
            15 => 0,
            16 => 0,
            17 => 0,
            18 => 0,
            19 => 0,
            20 => 0,
            21 => 0,
            22 => 0,
            23 => 0,
        ],
    ]);

    expect($cache->getHistoricalAverages())->toBe([
        'count'  => $transactionCount,
        'amount' => $totalAmount,
        'fee'    => $totalFees,
    ]);
    expect($cache->getLargestIdByAmount())->toBe($largestTransaction->id);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);
});

it('should not trigger event if nothing changed', function (): void {
    Event::fake();

    $this->travelTo(Carbon::parse('2024-04-08 15:22:19'));

    $networkStub = new NetworkStub(true, Carbon::now()->subDay(2));
    app()->singleton(NetworkContract::class, fn () => $networkStub);

    Transaction::factory(2)->delegateRegistration()->create([
        'amount' => 0,
        'fee'    => 9 * 1e8,
    ]);
    Transaction::factory(3)->transfer()->create([
        'amount' => 2000 * 1e8,
        'fee'    => 10 * 1e8,
    ]);
    Transaction::factory(4)->multipayment()->create([
        'amount' => 0,
        'fee'    => 11 * 1e8,
        'asset'  => [
            'payments' => [
                [
                    'amount' => 3000 * 1e8,
                ],
            ],
        ],
    ]);
    Transaction::factory()
        ->transfer()
        ->create([
            'amount' => 9000 * 1e8,
            'fee'    => 10 * 1e8,
        ]);

    Artisan::call('explorer:cache-transactions');

    Event::assertDispatchedTimes(TransactionDetails::class, 1);

    Event::fake();

    Artisan::call('explorer:cache-transactions');

    Event::assertDispatchedTimes(TransactionDetails::class, 0);
});

it('should trigger event if historical averages has changed', function (): void {
    Event::fake();

    $this->travelTo(Carbon::parse('2024-04-08 15:22:19'));

    $networkStub = new NetworkStub(true, Carbon::now()->subDay(2));
    app()->singleton(NetworkContract::class, fn () => $networkStub);

    Transaction::factory(2)->delegateRegistration()->create([
        'amount' => 0,
        'fee'    => 9 * 1e8,
    ]);
    Transaction::factory(3)->transfer()->create([
        'amount' => 2000 * 1e8,
        'fee'    => 10 * 1e8,
    ]);
    Transaction::factory(4)->multipayment()->create([
        'amount' => 0,
        'fee'    => 11 * 1e8,
        'asset'  => [
            'payments' => [
                [
                    'amount' => 3000 * 1e8,
                ],
            ],
        ],
    ]);
    $transaction = Transaction::factory()
        ->transfer()
        ->create([
            'amount' => 9000 * 1e8,
            'fee'    => 10 * 1e8,
        ]);

    Artisan::call('explorer:cache-transactions');

    Event::assertDispatchedTimes(TransactionDetails::class, 1);

    Event::fake();

    $transaction->amount = 10000 * 1e8;
    $transaction->save();

    Artisan::call('explorer:cache-transactions');

    Event::assertDispatchedTimes(TransactionDetails::class, 1);
});

it('should trigger event if largest transaction has changed', function (): void {
    Event::fake();

    $this->travelTo(Carbon::parse('2024-04-08 15:22:19'));

    $networkStub = new NetworkStub(true, Carbon::now()->subDay(2));
    app()->singleton(NetworkContract::class, fn () => $networkStub);

    $cache = new TransactionCache();

    Transaction::factory(2)->delegateRegistration()->create([
        'amount' => 0,
        'fee'    => 9 * 1e8,
    ]);
    Transaction::factory(3)->transfer()->create([
        'amount' => 2000 * 1e8,
        'fee'    => 10 * 1e8,
    ]);
    Transaction::factory(4)->multipayment()->create([
        'amount' => 0,
        'fee'    => 11 * 1e8,
        'asset'  => [
            'payments' => [
                [
                    'amount' => 3000 * 1e8,
                ],
            ],
        ],
    ]);
    $largest = Transaction::factory()
        ->transfer()
        ->create([
            'amount' => 9000 * 1e8,
            'fee'    => 10 * 1e8,
        ]);

    $smallest = Transaction::factory()
        ->transfer()
        ->create([
            'amount' => 1 * 1e8,
            'fee'    => 10 * 1e8,
        ]);

    Artisan::call('explorer:cache-transactions');

    expect($cache->getLargestIdByAmount())->toBe($largest->id);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);

    Event::fake();

    $smallest->amount = 10000 * 1e8;
    $smallest->save();

    Artisan::call('explorer:cache-transactions');

    expect($cache->getLargestIdByAmount())->toBe($smallest->id);

    Event::assertDispatchedTimes(TransactionDetails::class, 1);
});
