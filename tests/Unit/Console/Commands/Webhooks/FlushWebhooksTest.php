<?php

declare(strict_types=1);

use App\Models\Webhook;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

beforeEach(fn () => Webhook::truncate());

it('should execute the command', function () {
    Http::fake(Http::response(null, 200));

    Webhook::factory(3)->create();

    Artisan::call('ark:webhook:flush');

    expect(Webhook::count())->toBe(0);
    expect(Artisan::output())->toBe('');
});

it('should do nothing if no webhooks', function () {
    expect(Webhook::count())->toBe(0);

    Artisan::call('ark:webhook:flush');

    expect(Webhook::count())->toBe(0);
    expect(Artisan::output())->toBe('');
});

it('should override host', function () {
    Http::fake(Http::response(null, 200));

    $webhooks = Webhook::factory(3)->create();

    Artisan::call('ark:webhook:flush', [
        '--host' => '1.2.3.4',
    ]);

    expect(Webhook::count())->toBe(0);
    expect(Artisan::output())->toBe('');

    foreach ($webhooks as $webhook) {
        Http::assertSent(function ($request) use ($webhook) {
            if ($request->method() !== 'DELETE') {
                return false;
            }

            return $request->url() === 'http://1.2.3.4:'.$webhook->port.'/api/webhooks/'.$webhook->id;
        });
    }
});

it('should override port', function () {
    Http::fake(Http::response(null, 200));

    $webhooks = Webhook::factory(3)->create(['port' => '4567']);

    Artisan::call('ark:webhook:flush', [
        '--port' => '1234',
    ]);

    expect(Webhook::count())->toBe(0);
    expect(Artisan::output())->toBe('');

    foreach ($webhooks as $webhook) {
        Http::assertSent(function ($request) use ($webhook) {
            if ($request->method() !== 'DELETE') {
                return false;
            }

            return $request->url() === 'http://'.$webhook->host.':1234/api/webhooks/'.$webhook->id;
        });
    }
});
