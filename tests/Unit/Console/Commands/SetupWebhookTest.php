<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

it('should execute the command', function () {
    Http::fake(Http::response([
        'data' => [
            'id'    => 'responseId',
            'token' => 'random-token',
        ],
    ], 200));

    Artisan::call('ark:webhook:setup', [
        '--host' => '1.2.3.4',
    ]);

    $output = Artisan::output();

    expect($output)->toContain('ID: responseId');
    expect($output)->toContain('Token: random-token');
});

it('should require host', function () {
    Artisan::call('ark:webhook:setup');

    $output = Artisan::output();

    expect($output)->toContain('Missing [host] argument.');
});

it('should require port', function () {
    Artisan::call('ark:webhook:setup', [
        '--host' => '1.2.3.4',
        '--port' => null,
    ]);

    $output = Artisan::output();

    expect($output)->toContain('Missing [port] argument.');
});