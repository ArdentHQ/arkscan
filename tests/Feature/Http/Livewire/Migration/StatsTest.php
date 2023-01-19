<?php

declare(strict_types=1);

use App\Http\Livewire\Migration\Stats;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

it('should calculate stats correctly', function () {
    Config::set('explorer.migration.address', 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj');

    $wallet = Wallet::factory()->create([
        'address' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
        'balance' => 9876543210,
    ]);

    Transaction::factory()->create([
        'recipient_id' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
        'amount'       => 9876543210,
    ]);

    (new NetworkCache())->setTotalSupply(function (): float {
        return (float) 91234567890;
    });

    Livewire::test(Stats::class)
        ->assertViewHas('amountMigrated', '98.7654321')
        ->assertViewHas('remainingSupply', '813.5802468')
        ->assertViewHas('percentage', '10.75%')
        ->assertViewHas('walletsMigrated', '1');
});

it('should calculate stats despite unmatched transaction criteria', function () {
    Config::set('explorer.migration.address', 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj');

    $wallet = Wallet::factory()->create([
        'address' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
        'balance' => 9876543210,
    ]);

    Transaction::factory()->create([
        'recipient_id' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
        'fee'          => '1000000', // 0.01
        'amount'       => '20000000', // 0.2
    ]);

    (new NetworkCache())->setTotalSupply(function (): float {
        return (float) 91234567890;
    });

    Livewire::test(Stats::class)
        ->assertViewHas('amountMigrated', '98.7654321')
        ->assertViewHas('remainingSupply', '813.5802468')
        ->assertViewHas('percentage', '10.75%')
        ->assertViewHas('walletsMigrated', '1');
});

it('should handle no migration wallet', function () {
    Config::set('explorer.migration.address', 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj');

    $wallet = Wallet::factory()->create([
        'balance' => 9876543210,
    ]);

    expect($wallet->address)->not->toBe('DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj');

    Livewire::test(Stats::class)
        ->assertViewHas('amountMigrated', '0');
});

it('should cache migrated wallet count', function () {
    Config::set('explorer.migration.address', 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj');

    (new NetworkCache())->setTotalSupply(function (): float {
        return (float) 91234567890;
    });

    $this->travelTo(Carbon::parse('2022-01-06 00:15:00'));

    Livewire::test(Stats::class)
        ->assertViewHas('walletsMigrated', 0);

    $this->travelTo(Carbon::parse('2022-01-06 00:19:00'));

    Livewire::test(Stats::class)
        ->assertViewHas('walletsMigrated', 0);

    $this->travelTo(Carbon::parse('2022-01-06 00:21:00'));

    Transaction::factory()->create([
        'recipient_id' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
        'amount'       => 9876543210,
    ]);

    Livewire::test(Stats::class)
        ->assertViewHas('walletsMigrated', 1);
});
