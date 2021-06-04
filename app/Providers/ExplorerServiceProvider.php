<?php

declare(strict_types=1);

namespace App\Providers;

use ArkEcosystem\Crypto\Configuration\Network as NetworkConfiguration;
use App\Contracts\Network;
use App\Services\Blockchain\NetworkFactory;
use Illuminate\Support\ServiceProvider;

final class ExplorerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $network = NetworkFactory::make(config('explorer.network'));

        $this->app->singleton(
            Network::class,
            fn () => $network
        );

        // Used for crypto calculations, e.g. multisig address derivation
        NetworkConfiguration::set($network->config());
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
