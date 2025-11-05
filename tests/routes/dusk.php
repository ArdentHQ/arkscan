<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

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
