<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\MarketDataProvider;
use App\Contracts\Services\GasTracker as GasTrackerContract;
use App\Contracts\Services\Monitor\MissedBlocksCalculator as MissedBlocksCalculatorContract;
use App\Facades\Network;
use App\Services\BigNumber;
use App\Services\GasTracker;
use App\Services\Monitor\MissedBlocksCalculator;
use ARKEcosystem\Foundation\DataBags\DataBag;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
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
            fn () => new (Config::get('arkscan.market_data_provider_service'))
        );

        $this->app->singleton(
            MissedBlocksCalculatorContract::class,
            fn () => new (MissedBlocksCalculator::class)()
        );

        $this->app->singleton(
            GasTrackerContract::class,
            fn () => new (GasTracker::class)()
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

        RateLimiter::for('coingecko_api_rate', fn () => Limit::perMinute(10));
    }

    private function registerCollectionMacros(): void
    {
        Collection::macro('sumBigNumber', function (string $key) {
            /** @var Collection $collection */
            $collection = $this;

            return $collection->reduce(function ($result, $item) use ($key) {
                /** @var array $item */
                return $result->plus($item[$key]->valueOf());
            }, BigNumber::zero());
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
            'validators' => [
                'title'       => 'Validator Monitor | ARKScan | Cryptocurrency Block Explorer',
                'description' => 'Monitor Validator activity for the ARK Public Network. See Validator rankings and track Voting Power in the ARK Blockchain.',
            ],
            'wallets'   => [
                'title'       => 'Wallet Addresses | ARK Scan | Cryptocurrency Block Explorer',
                'description' => 'See wallet addresses on the ARK Scan. Track balances and see transaction activity for wallet addresses on the ARK Public Nework',
            ],
            '*'         => [
                'title'       => 'ARK Scan | Cryptocurrency Block Explorer',
                'description' => 'View cryptocurrency transactions and track cryptocurrency balances. A simple block explorer to monitor Blockchain activity on the ARK Public Network.',
            ],
        ]);
    }
}
