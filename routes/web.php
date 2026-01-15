<?php

declare(strict_types=1);

use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\ExchangesController;
use App\Http\Controllers\Inertia\BlocksListController;
use App\Http\Controllers\Inertia\CompatibleWalletsController;
use App\Http\Controllers\Inertia\HomeController;
use App\Http\Controllers\Inertia\ShowBlockController;
use App\Http\Controllers\Inertia\ShowTransactionController;
use App\Http\Controllers\Inertia\StatisticsController;
use App\Http\Controllers\Inertia\SupportController;
use App\Http\Controllers\Inertia\TransactionsController;
use App\Http\Controllers\Inertia\ValidatorMonitorController;
use App\Http\Controllers\Inertia\ValidatorsController;
use App\Http\Controllers\Inertia\WalletController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShowBlockController as LegacyShowBlockController;
use App\Http\Controllers\ShowTransactionController as LegacyShowTransactionController;
use App\Http\Controllers\SupportController as LegacySupportController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\WebhooksController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', HomeController::class)->name('home');
Route::get('/validators/{view?}', ValidatorsController::class)->name('validators');
Route::get('/validator-monitor', ValidatorMonitorController::class)->name('validator-monitor');

Route::get('/blocks', BlocksListController::class)->name('blocks');
Route::get('/blocks/{block}', ShowBlockController::class)->name('block');
Route::get('/old-blocks/{block}', LegacyShowBlockController::class)->name('old-block');

Route::get('/transactions', TransactionsController::class)->name('transactions');
Route::get('/transactions/{transaction}', ShowTransactionController::class)->name('transaction');
Route::get('/old-transactions/{transaction}', LegacyShowTransactionController::class)->name('old-transaction');

Route::view('/top-accounts', 'app.top-accounts')->name('top-accounts');
Route::get('/addresses/{wallet}/{view?}', WalletController::class)->name('wallet');

Route::get('/wallets/{wallet}/', function (Wallet $wallet) {
    return redirect()->route('wallet', $wallet);
});
Route::get('/wallets/{wallet}/voters', function (Wallet $wallet) {
    return redirect()->route('wallet', ['wallet' => $wallet, 'view' => 'voters']);
});
Route::get('/wallets/{wallet}/blocks', function (Wallet $wallet) {
    return redirect()->route('wallet', ['wallet' => $wallet, 'view' => 'blocks']);
});

Route::get('/statistics', StatisticsController::class)->name('statistics');
Route::view('/old-statistics', 'app.statistics')->name('old-statistics');

// Keep the route name as contact for use with the foundation component
Route::get('/support', SupportController::class)->name('contact');
Route::get('/support-old', [LegacySupportController::class, 'index'])->name('contact-old');
Route::post('support', [SupportController::class, 'submit'])
    ->middleware([
        ProtectAgainstSpam::class,
        'throttle:5,60',
    ]);
Route::post('support-old', [LegacySupportController::class, 'handle'])
    ->middleware([
        ProtectAgainstSpam::class,
        'throttle:5,60',
    ]);

// Explorer 3.0 BC - Remove after some time!
Route::redirect('/top-wallets', '/top-accounts');
Route::redirect('/wallets', '/top-accounts');
Route::get('/block/{block}', fn (Block $block) => redirect()->route('block', ['block' => $block]));
Route::get('/transaction/{transaction}', fn (Transaction $transaction) => redirect()->route('transaction', ['transaction' => $transaction->hash]));
Route::get('/wallet/{wallet}', fn (Wallet $wallet) => redirect()->route('wallet', ['wallet' => $wallet]));

Route::get('/compatible-wallets', CompatibleWalletsController::class)->name('compatible-wallets');
Route::post('/compatible-wallets', [CompatibleWalletsController::class, 'submit'])
    ->middleware(['throttle:3,3600'])
    ->name('compatible-wallets.submit');

Route::view('/compatible-wallets-old', 'app.compatible-wallets')->name('compatible-wallets-old');

Route::get('/exchanges', ExchangesController::class)->name('exchanges');

Route::post('/webhooks', WebhooksController::class)
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->name('webhooks');

Route::post('/currency/update', [CurrencyController::class, 'update'])
    ->name('currency.update');

Route::post('/theme/update', [ThemeController::class, 'update'])
    ->name('theme.update');

Route::get('/navbar/search', [SearchController::class, 'index'])
    ->name('navbar-search.index');

Route::post('/navbar/search/redirect', [SearchController::class, 'redirect'])
    ->name('navbar-search.redirect');
