<?php

declare(strict_types=1);

use App\Services\CryptoCompare;
use Illuminate\Support\Facades\Http;

it('should fetch the price for the given pair', function () {
    Http::fake([
        'cryptocompare.com/*' => Http::response(['USD' => 0.2907]),
    ]);

    expect(CryptoCompare::price('ARK', 'USD'))->toBe(0.2907);
});
