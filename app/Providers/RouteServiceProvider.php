<?php

declare(strict_types=1);

namespace App\Providers;

use App\Exceptions\BlockNotFoundException;
use App\Exceptions\TransactionNotFoundException;
use App\Exceptions\WalletNotFoundException;
use App\Facades\Network;
use App\Facades\Wallets;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use ArkEcosystem\Crypto\Identities\Address;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Throwable;

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

        Route::bind('wallet', function (string $walletID): Wallet {
            if (strlen($walletID) === 34) {
                abort_unless(Address::validate($walletID, Network::config()), 404);
            }

            try {
                return strlen($walletID) === 34
                        ? Wallets::findByAddress($walletID)
                        : Wallets::findByUsername($walletID);
            } catch (Throwable) {
                throw (new WalletNotFoundException())->setModel(Wallet::class, [$walletID]);
            }
        });

        Route::bind('transaction', function (string $transactionID): Transaction {
            $transaction = Transaction::find($transactionID);

            if ($transaction === null) {
                throw (new TransactionNotFoundException())->setModel(Transaction::class, [$transactionID]);
            }

            return $transaction;
        });

        Route::bind('block', function (string $blockID): Block {
            $block = Block::find($blockID);

            if ($block === null) {
                throw (new BlockNotFoundException())->setModel(Block::class, [$blockID]);
            }

            return $block;
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
