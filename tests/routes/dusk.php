<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/transactions', function () {
    return response()->json([
        'data' => [
            [
                'hash'      => 'dusk-transaction-1',
                'timestamp' => [
                    'epoch' => now()->timestamp,
                ],
                'from'  => '0x0000000000000000000000000000000000000000',
                'to'    => '0x0000000000000000000000000000000000000000',
                'value' => '125000000',
                'fee'   => '1000000',
            ],
        ],
        'meta' => [
            'count' => 2,
        ],
    ]);
});

// Route::get('/dusk-api/validators/{address}/blocks', function ($address) {
//     return response()->json(config('dusk.blocks_response', [
//         'data' => [],
//         'meta' => [
//             'count' => 0,
//         ],
//     ]));
// });
