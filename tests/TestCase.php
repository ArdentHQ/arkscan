<?php

declare(strict_types=1);

namespace Tests;

use App\Contracts\MarketDataProvider;
use App\Services\MarketDataProviders\CryptoCompare;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();

        Config::set('explorer.networks.development.knownWallets', null);
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $app->singleton(
            MarketDataProvider::class,
            fn () => new CryptoCompare()
        );

        return $app;
    }

    /**
     * Refresh a conventional test database.
     *
     * @return void
     */
    protected function refreshTestDatabase()
    {
        $this->artisan('migrate:fresh', $this->migrateFreshUsing());

        $this->artisan('migrate:fresh', [
            '--database' => 'explorer',
            '--path'     => 'tests/migrations',
        ]);
    }
}
