<?php

declare(strict_types=1);

use App\Jobs\CacheCurrenciesHistory as CacheCurrenciesHistoryJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;

it('should execute the job', function () {
    Bus::fake();

    Config::set('currencies', [
        'usd' => [
            'currency' => 'USD',
            'locale'   => 'en_US',
        ],
    ]);

    Config::set('explorer.networks.development.canBeExchanged', true);

    $this->artisan('explorer:cache-currencies-history');

    Bus::assertDispatched(CacheCurrenciesHistoryJob::class, fn ($job) => $job->source === 'DARK' && $job->currency === 'USD');
});

it('should execute the command with no delay command', function () {
    Bus::fake();

    Config::set('currencies', [
        'usd' => [
            'currency' => 'USD',
            'locale'   => 'en_US',
        ],
    ]);

    Config::set('explorer.networks.development.canBeExchanged', true);

    $this->artisan('explorer:cache-currencies-history --no-delay');

    Bus::assertDispatched(CacheCurrenciesHistoryJob::class, fn ($job) => $job->source === 'DARK' && $job->currency === 'USD');
});

it('should not execute the job if cannot be exchanged', function () {
    Bus::fake();

    Config::set('currencies', [
        'usd' => [
            'currency' => 'USD',
            'locale'   => 'en_US',
        ],
    ]);

    Config::set('explorer.networks.development.canBeExchanged', false);

    $this->artisan('explorer:cache-currencies-history');

    Bus::assertNotDispatched(CacheCurrenciesHistoryJob::class);
});
