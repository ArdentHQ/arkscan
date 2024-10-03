<?php

declare(strict_types=1);

use App\Models\Webhook;
use Illuminate\Support\Facades\Artisan;

beforeEach(fn () => Webhook::truncate());

it('should execute the command', function () {
    $webhooks = Webhook::factory(3)->create();

    Artisan::call('ark:webhook:list');

    $output = Artisan::output();

    foreach ($webhooks as $webhook) {
        expect($output)->toContain($webhook->id);
        expect($output)->toContain($webhook->token);
        expect($output)->toContain($webhook->event);
        expect($output)->toContain($webhook->host.':'.$webhook->port);
    }
});

it('should output if no webhooks', function () {
    Artisan::call('ark:webhook:list');

    expect(Artisan::output())->toContain('There are currently no webhooks.');
});
