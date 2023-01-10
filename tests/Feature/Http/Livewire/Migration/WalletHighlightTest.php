<?php

declare(strict_types=1);

use App\Http\Livewire\Migration\WalletHighlight;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

it('should render the component correctly', function () {
    Config::set('explorer.migration_address', 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj');

    $wallet = Wallet::factory()->create([
        'address' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
        'balance' => 9876543210,
    ]);

    Transaction::factory()->create([
        'recipient_id' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
        'amount'       => 9876543210,
    ]);

    (new NetworkCache())->setSupply(function (): float {
        return (float) 91234567890;
    });

    Livewire::test(WalletHighlight::class)
        ->assertViewHas('amountMigrated', '98.7654321')
        ->assertViewHas('remainingSupply', '813.5802468')
        ->assertViewHas('percentage', '10.75%')
        ->assertSee(trans('pages.migration.stats.migration_wallet'));
});
