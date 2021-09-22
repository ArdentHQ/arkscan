<?php

declare(strict_types=1);

namespace Tests;

use App\Contracts\MarketDataProvider;
use App\Services\MarketDataProviders\CryptoCompare;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

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
        Artisan::call('migrate:fresh', [
            '--database' => 'explorer',
            '--path'     => 'tests/migrations',
        ]);
    }
}
