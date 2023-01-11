<?php

declare(strict_types=1);

use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use Illuminate\Support\Facades\Config;

it('should render the page without any errors', function () {
    Config::set('explorer.migration_address', 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj');

    $wallet = Wallet::factory()->create([
        'address' => 'DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj',
        'balance' => 9876543210,
    ]);

    (new NetworkCache())->setTotalSupply(function (): float {
        return (float) 91234567890;
    });

    $this
        ->get(route('migration'))
        ->assertOk()
        ->assertSee('DENGkAwEfRvhhHKZYdEfQ1P3MEoRvPkHYj')
        ->assertSee('99 DARK')
        ->assertSee('813.58 DARK');
});
