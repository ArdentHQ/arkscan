<?php

declare(strict_types=1);

use App\Models\Webhook;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

beforeEach(fn () => Webhook::truncate());

it('should execute the command', function () {
    Http::fake(Http::response([
        'data' => [
            'id'    => 'responseId',
            'token' => 'random-token',
        ],
    ], 201));

    Artisan::call('ark:webhook:setup', [
        '--host'  => '1.2.3.4',
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

it('should require event', function () {
    Artisan::call('ark:webhook:setup', [
        '--host' => '1.2.3.4',
        '--port' => 1234,
    ]);

    $output = Artisan::output();

    expect($output)->toContain('Missing [event] argument.');
});

it('should error if webhook request fails with a message', function () {
    Http::fake(Http::response([
        'message' => 'unknown error',
    ], 403));

    Artisan::call('ark:webhook:setup', [
        '--host'  => '1.2.3.4',
        '--port'  => 1234,
        '--event' => 'test.event',
    ]);

    $output = Artisan::output();

    expect($output)->toContain('Could not connect to core webhooks endpoint: unknown error');
});

it('should error if webhook request fails with a non-2xx status code', function () {
    Http::fake(Http::response(null, 403));

    Artisan::call('ark:webhook:setup', [
        '--host'  => '1.2.3.4',
        '--port'  => 1234,
        '--event' => 'test.event',
    ]);

    $output = Artisan::output();

    expect($output)->toContain('There was a problem with the webhook request: 403');
});

it('should error if webhook request throws an exception', function () {
    Http::fake(Http::response(function () {
        throw new Exception('Oops');
    }, 403));

    Artisan::call('ark:webhook:setup', [
        '--host'  => '1.2.3.4',
        '--port'  => 1234,
        '--event' => 'test.event',
    ]);

    $output = Artisan::output();

    expect($output)->toContain('Could not connect to core webhooks endpoint: Oops');
});
