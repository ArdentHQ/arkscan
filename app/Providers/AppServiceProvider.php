<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\MarketDataProvider;
use App\Services\BigNumber;
use ARKEcosystem\Foundation\DataBags\DataBag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Model::unguard();

        $this->app->singleton(
            MarketDataProvider::class,
            fn () => new (Config::get('explorer.market_data_provider_service'))
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerCollectionMacros();

        $this->registerDataBags();

        Fortify::loginView(fn () => abort(404));
    }

    private function registerCollectionMacros(): void
    {
        Collection::macro('sumBigNumber', function (string $key) {
            /** @var Collection $collection */
            $collection = $this;

            return $collection->reduce(function ($result, $item) use ($key) {
                return $result->plus($item[$key]->valueOf());
            }, BigNumber::new(0));
        });

        Collection::macro('ksort', function (): Collection {
            /* @phpstan-ignore-next-line */
            ksort($this->items);

            /* @phpstan-ignore-next-line */
            return collect($this->items);
        });
    }

    private function registerDataBags(): void
    {
        DataBag::register('metatags', [
            'delegates' => [
                'title'       => 'Delegate Monitor | ARKScan | Cryptocurrency Block Explorer',
                'description' => 'Monitor Delegate activity for the ARK Public Network. See Delegate rankings and track Voting Power in the ARK Blockchain.',
            ],
            'wallets' => [
                'title'       => 'Wallet Addresses | ARKScan | Cryptocurrency Block Explorer',
                'description' => 'See wallet addresses on the ARKScan. Track balances and see transaction activity for wallet addresses on the ARK Public Nework',
            ],
            '*' => [
                'title'       => 'ARKScan | Cryptocurrency Block Explorer',
                'description' => 'View cryptocurrency transactions and track cryptocurrency balances. A simple block explorer to monitor Blockchain activity on the ARK Public Network.',
            ],
        ]);
    }
}
