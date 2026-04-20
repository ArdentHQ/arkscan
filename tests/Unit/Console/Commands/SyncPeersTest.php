<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;

it('should execute the command', function () {
    Http::fake([
        '*/peers' => Http::response([
            'data' => [],
        ]),
    ]);

    $this->artisan('explorer:sync-peers')
        ->expectsOutputToContain('Synced 0 new peer(s).')
        ->assertExitCode(0);
});
