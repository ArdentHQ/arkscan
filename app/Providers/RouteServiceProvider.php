<?php

declare(strict_types=1);

namespace App\Providers;

use App\Facades\Network;
use App\Models\Wallet;
use ArkEcosystem\Crypto\Identities\Address;
use ARKEcosystem\UserInterface\UI;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

final class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function (): void {
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        Route::bind('wallet', function (string $value): Wallet {
            abort_unless(Address::validate($value, Network::config()), 404);

            try {
                return Wallet::where('address', $value)->firstOrFail();
            } catch (\Throwable $th) {
                UI::useErrorMessage(404, trans('general.wallet_not_found', [$value]));

                abort(404);
            }
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    private function configureRateLimiting()
    {
        RateLimiter::for('api', fn (): Limit => Limit::perMinute(60));
    }
}
