<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Network;
use App\Services\Blockchain\NetworkFactory;
use ArkEcosystem\Crypto\Configuration\Network as NetworkConfiguration;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

final class ArkscanServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            Network::class,
            fn ($app) => NetworkFactory::make($app['config']['arkscan']['network'])
        );

        // Used for crypto calculations, e.g. multisig address derivation
        NetworkConfiguration::set(NetworkFactory::make(Arr::get($this->app->get('config')->get('arkscan'), 'network'))->config());
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
