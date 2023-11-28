<?php

declare(strict_types=1);

use App\Http\Livewire\FiatValue;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

it('should render', function () {
    Config::set('arkscan.network', 'production');

    (new NetworkStatusBlockCache())->setPrice('ARK', 'USD', 2);

    Livewire::test(FiatValue::class, [
        'amount' => 10,
    ])->assertSeeInOrder([
        '$20.00',
        'USD',
    ]);
});

it('should render with timestamp', function () {
    $this->travelTo(Carbon::parse('2020-10-19'));

    Config::set('arkscan.network', 'production');

    (new CryptoDataCache())->setPrices('USD.week', collect([
        '2020-10-19' => 4,
    ]));
    (new NetworkStatusBlockCache())->setPrice('ARK', 'USD', 2);

    Livewire::test(FiatValue::class, [
        'amount'    => 12,
        'timestamp' => Timestamp::fromUnix(Carbon::parse('2020-10-19')->unix())->unix(),
    ])->assertSeeInOrder([
        '$48.00',
        'USD',
    ]);
});
