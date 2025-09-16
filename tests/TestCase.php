<?php

declare(strict_types=1);

namespace Tests;

use App\Contracts\MarketDataProvider;
use App\Services\MarketDataProviders\CryptoCompare;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\View\Compilers\BladeCompiler;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected $connectionsToTransact = ['pgsql', 'explorer'];

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();

        Config::set('arkscan.networks.development.knownWallets', null);

        $this->app->singleton(
            MarketDataProvider::class,
            fn () => new CryptoCompare()
        );
    }

    protected function tearDown(): void
    {
        Artisan::call('view:clear');

        // /** @var BladeCompiler $blade */
        // $blade = app('blade.compiler');   // stable binding in Laravel
        // dd($blade);
        // $blade->forgetCompiledOrNotExpired();

        parent::tearDown();

        // // Cache::flush();
        // // $this->artisan('view:clear');

        // // dd(app());

        // app('view.engine.resolver')
        //     ->resolve('blade')
        //     ->forgetCompiledOrNotExpired();

        // $path = storage_path('framework/views');
        // $files = new Filesystem();
        // foreach ($files->glob("{$path}/*") as $view) {
        //     $files->delete($view);
        // }
    }

    /**
     * Refresh a conventional test database.
     *
     * @return void
     */
    protected function refreshTestDatabase()
    {
        if (! RefreshDatabaseState::$migrated) {
            $this->artisan('migrate:fresh', [
                ...$this->migrateFreshUsing(),
                '--database' => 'pgsql',
                '--path'     => 'database/migrations',
            ]);

            $this->artisan('migrate:fresh', [
                ...$this->migrateFreshUsing(),
                '--database' => 'explorer',
                '--path'     => 'tests/migrations',
            ]);

            $this->app[Kernel::class]->setArtisan(null);

            RefreshDatabaseState::$migrated = true;
        }

        $this->beginDatabaseTransaction();
    }
}
