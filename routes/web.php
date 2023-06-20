<?php

declare(strict_types=1);

use App\Http\Controllers\ContactController;
use App\Http\Controllers\ExchangesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListBlocksByWalletController;
use App\Http\Controllers\ShowBlockController;
use App\Http\Controllers\ShowTransactionController;
use App\Http\Controllers\ShowWalletController;
use App\Http\Controllers\TransactionsController;
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
Route::view('/delegates', 'app.delegates')->name('delegates');

Route::view('/blocks', 'blocks')->name('blocks');
Route::get('/blocks/{block}', ShowBlockController::class)->name('block');

Route::get('/transactions', TransactionsController::class)->name('transactions');
Route::get('/transactions/{transaction}', ShowTransactionController::class)->name('transaction');

Route::view('/top-accounts', 'app.top-accounts')->name('top-accounts');
Route::get('/addresses/{wallet}', ShowWalletController::class)->name('wallet');
Route::get('/addresses/{wallet}?view=voters', ShowWalletController::class)->name('wallet.voters');
Route::get('/addresses/{wallet}/blocks', ListBlocksByWalletController::class)->name('wallet.blocks');

Route::get('/wallets/{wallet}', function (Wallet $wallet) {
    return redirect()->route('wallet', $wallet);
});
Route::get('/wallets/{wallet}/voters', function (Wallet $wallet) {
    return redirect()->route('wallet', [
        'wallet' => $wallet,
        'view'   => 'voters',
    ]);
});
Route::get('/wallets/{wallet}/blocks', function (Wallet $wallet) {
    return redirect()->route('wallet.blocks', $wallet);
});

Route::view('/statistics', 'app.statistics')->name('statistics');
Route::get('/support', [ContactController::class, 'index'])->name('contact');
Route::post('support', [ContactController::class, 'handle'])
    ->middleware([
        ProtectAgainstSpam::class,
        'throttle:5,60',
    ]);

// Explorer 3.0 BC - Remove after some time!
Route::redirect('/delegate-monitor', '/delegates');
Route::redirect('/top-wallets', '/top-accounts');
Route::redirect('/wallets', '/top-accounts');
Route::get('/block/{block}', fn (Block $block) => redirect()->route('block', ['block' => $block]));
Route::get('/transaction/{transaction}', fn (Transaction $transaction) => redirect()->route('transaction', ['transaction' => $transaction]));
Route::get('/wallet/{wallet}', fn (Wallet $wallet) => redirect()->route('wallet', ['wallet' => $wallet]));

Route::view('/compatible-wallets', 'app.compatible-wallets')->name('compatible-wallets');
Route::get('/exchanges', ExchangesController::class)->name('exchanges');
