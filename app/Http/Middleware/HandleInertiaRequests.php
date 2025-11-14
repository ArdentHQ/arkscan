<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\DTO\Inertia\IConfigArkconnect;
use App\DTO\Inertia\IConfigPagination;
use App\DTO\Inertia\IConfigProductivity;
use App\DTO\Inertia\ICurrency;
use App\DTO\Inertia\IRequestData;
use App\DTO\Inertia\IPriceTickerData;
use App\Facades\Network;
use App\Facades\Settings;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\ExchangeRate;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'layouts.inertia';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...IRequestData::from([
                'network'            => Network::data(),
                'settings'           => Settings::data(),
                'productivity'       => IConfigProductivity::from(config('arkscan.productivity')),
                'arkconnectConfig'   => IConfigArkconnect::from([
                    'enabled'  => config('arkscan.arkconnect.enabled'),
                    'vaultUrl' => config('arkscan.urls.vault_url'),
                ]),
                'currencies'           => array_map(fn (array $currency) => ICurrency::from($currency), config('currencies.currencies')),
                'pagination'           => IConfigPagination::from(config('arkscan.pagination')),
                'broadcasting'         => config('broadcasting.default'),
                'networkName'          => fn () => config('arkscan.network'),
                'isDownForMaintenance' => fn () => app()->isDownForMaintenance(),
                'priceTickerData'      => fn () => IPriceTickerData::from([
                    'currency' => Settings::currency(),
                    'isPriceAvailable' => (new NetworkStatusBlockCache())->getIsAvailable(Network::currency(), Settings::currency()),
                    'priceExchangeRate' => random_int(1, 1000000), // ExchangeRate::currentRate(),
                ]),
            ])->toArray(),
            ...parent::share($request),
        ];
    }
}
