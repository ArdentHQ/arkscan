<?php

declare(strict_types=1);

use App\Models\Webhook;
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
        '--event' => 'test.event',
    ]);

    $output = Artisan::output();

    expect($output)->toContain('ID: responseId');
    expect($output)->toContain('Token: random-token');

    $webhook = Webhook::first();

    expect($webhook)->not->toBeNull();
    expect($webhook->id)->toBe('responseId');
    expect($webhook->token)->toBe('random-token');
    expect($webhook->host)->toBe('1.2.3.4');
    expect($webhook->port)->toBe(4004);
    expect($webhook->event)->toBe('test.event');
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
