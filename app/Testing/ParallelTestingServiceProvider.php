<?php

declare(strict_types=1);

namespace App\Testing;

use App\Testing\Concerns\TestDatabasesWithMultipleConnections;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\ParallelTesting;

final class ParallelTestingServiceProvider extends ServiceProvider implements DeferrableProvider
{
    use TestDatabasesWithMultipleConnections;

    /**
     * Boot the application's service providers.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->bootTestDatabase();
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->app->singleton(ParallelTesting::class, function () {
                return new ParallelTesting($this->app);
            });
        }
    }
}
