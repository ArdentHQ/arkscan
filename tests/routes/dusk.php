<?php

declare(strict_types=1);

use App\Http\Controllers\Inertia\ExchangesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Tests\routes\Overrides\HandleInertiaRequestsCanBeExchanged;

Route::get('/transactions', function () {
    $default = [
        'data' => [],
        'meta' => [
            'count' => 0,
        ],
    ];

    $response = Cache::tags(['dusk'])->get('dusk.transactions_response', $default);

    return response()->json($response);
});

Route::get('/validators/{address}/blocks', function ($address) {
    $default = [
        'data' => [],
        'meta' => [
            'count' => 0,
        ],
    ];

    $response = Cache::tags(['dusk'])->get('dusk.blocks_response', $default);

    return response()->json($response);
});

Route::get('/exchanges/testing-can-be-exchanged', fn (Request $request) => (new ExchangesController)($request))
    ->middleware([
        HandleInertiaRequestsCanBeExchanged::class,
        'web',
    ])
    ->name('dusk:exchanges:testing-can-be-exchanged');
