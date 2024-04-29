<?php

namespace App\Testing;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Testing\ParallelTesting;
use Illuminate\Support\ServiceProvider;
use App\Testing\Concerns\TestDatabasesWithMultipleConnections;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\ParallelTesting as ParallelTestingFacade;

class ParallelTestingServiceProvider extends ServiceProvider implements DeferrableProvider
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
            // $this->setupParallelTesting();
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

    // private function setupParallelTesting(): void
    // {
    //     ParallelTestingFacade::setUpTestDatabase(function ($database, $token) {
    //         dd($database);
    //         // Artisan::call('migrate:fresh', [
    //         //     '--database' => 'pgsql',
    //         //     // '--database' => 'pgsql_test_'.$token,
    //         //     '--path'     => 'database/migrations',
    //         // ]);

    //         Artisan::call('migrate:fresh', [
    //             '--database' => 'explorer',
    //             // '--database' => 'explorer_test_'.$token,
    //             '--path'     => 'tests/migrations',
    //         ]);
    //     });
    // }
}
