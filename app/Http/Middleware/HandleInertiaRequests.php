<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Inertia\Middleware;
use App\Facades\Network;
use App\Facades\Settings;
use Illuminate\Http\Request;
use App\DTO\Inertia\ICurrency;
use App\DTO\Inertia\IRequestData;
use App\DTO\Inertia\IConfigArkconnect;
use App\DTO\Inertia\IConfigProductivity;

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
                'currencies'   => array_map(fn (array $currency) => ICurrency::from($currency), config('currencies.currencies')),
                'network'      => Network::toArray(),
                'productivity' => IConfigProductivity::from(config('arkscan.productivity')),
                'settings'     => Settings::data(),
                'arkconnect'   => IConfigArkconnect::from([
                    'enabled'  => config('arkscan.arkconnect.enabled'),
                    'vaultUrl' => config('arkscan.urls.vault_url'),
                ]),
            ])->toArray(),
            ...parent::share($request),
        ];
    }
}
