<?php

declare(strict_types=1);

use App\Models\Webhook;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

beforeEach(fn () => Webhook::truncate());

it('should execute the command', function ($arg, $value) {
    Http::fake(Http::response(null, 201));

    $webhook = Webhook::factory()->create();

    Artisan::call('ark:webhook:delete', [$arg => $webhook->{$value}]);

    expect(Webhook::count())->toBe(0);
    expect(Artisan::output())->toBe('');

    Http::assertSent(function ($request) use ($webhook) {
        if ($request->method() !== 'DELETE') {
            return false;
        }

        return $request->url() === 'http://'.$webhook->host.':'.$webhook->port.'/api/webhooks/'.$webhook->id;
    });
})->with([
    'token' => ['--token', 'token'],
    'id'    => ['--id', 'id'],
]);

it('should require token or id argument', function () {
    expect(Webhook::count())->toBe(0);

    Artisan::call('ark:webhook:delete', ['--host' => '1.2.3.4']);

    expect(Webhook::count())->toBe(0);
    expect(Artisan::output())->toContain('Missing [token] or [id] argument.');
});

it('should error if no webhooks', function ($args) {
    expect(Webhook::count())->toBe(0);

    Artisan::call('ark:webhook:delete', $args);

    expect(Webhook::count())->toBe(0);
    expect(Artisan::output())->toContain('Webhook does not exist.');
})->with([
    'token' => [['--token' => '1234']],
    'id'    => [['--id' => '1234']],
]);

it('should override host', function () {
    Http::fake(Http::response(null, 200));

    $webhook = Webhook::factory()->create();

    Artisan::call('ark:webhook:delete', [
        '--host'  => '1.2.3.4',
        '--token' => $webhook->token,
    ]);

    expect(Artisan::output())->toBe('');
    expect(Webhook::count())->toBe(0);

    Http::assertSent(function ($request) use ($webhook) {
        if ($request->method() !== 'DELETE') {
            return false;
        }

        return $request->url() === 'http://1.2.3.4:'.$webhook->port.'/api/webhooks/'.$webhook->id;
    });
});

it('should override port', function () {
    Http::fake(Http::response(null, 200));

    $webhook = Webhook::factory()->create();

    Artisan::call('ark:webhook:delete', [
        '--port' => '1234',
        '--id'   => $webhook->id,
    ]);

    expect(Artisan::output())->toBe('');
    expect(Webhook::count())->toBe(0);

    Http::assertSent(function ($request) use ($webhook) {
        return $request->url() === 'http://'.$webhook->host.':1234/api/webhooks/'.$webhook->id;
    });
});

it('should error if webhook request fails with a message', function () {
    Http::fake(Http::response([
        'message' => 'unknown error',
    ], 403));

    $webhook = Webhook::factory()->create();

    Artisan::call('ark:webhook:delete', ['--id' => $webhook->id]);

    $output = Artisan::output();

    expect($output)->toContain('There was a problem removing the webhook: unknown error');
});

it('should error if webhook request fails with a non-null value', function () {
    Http::fake(Http::response(true, 200));

    $webhook = Webhook::factory()->create();

    Artisan::call('ark:webhook:delete', ['--id' => $webhook->id]);

    $output = Artisan::output();

    expect($output)->toContain('There was a problem removing the webhook: Unknown');
});

it('should error if webhook request fails with a non-200 status code', function () {
    Http::fake(Http::response(null, 403));

    $webhook = Webhook::factory()->create();

    Artisan::call('ark:webhook:delete', ['--id' => $webhook->id]);

    $output = Artisan::output();

    expect($output)->toContain('There was a problem removing the webhook: Unknown');
});

it('should error if webhook request throws an exception', function () {
    Http::fake(Http::response(function () {
        throw new \Exception('Oops');
    }, 403));

    $webhook = Webhook::factory()->create();

    Artisan::call('ark:webhook:delete', ['--id' => $webhook->id]);

    $output = Artisan::output();

    expect($output)->toContain('Could not connect to core webhooks endpoint: Oops');
});
