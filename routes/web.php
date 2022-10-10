<?php

declare(strict_types=1);

use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListBlocksByWalletController;
use App\Http\Controllers\ListVotersByWalletController;
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
Route::view('/search', 'app.search-results')->name('search');
Route::view('/delegates', 'app.delegates')->name('delegates');

Route::view('/blocks', 'blocks')->name('blocks');
Route::get('/blocks/{block}', ShowBlockController::class)->name('block');

Route::get('/transactions', TransactionsController::class)->name('transactions');
Route::get('/transactions/{transaction}', ShowTransactionController::class)->name('transaction');

Route::view('/wallets', 'app.wallets')->name('wallets');
Route::get('/wallets/{wallet}', ShowWalletController::class)->name('wallet');
Route::get('/wallets/{wallet}/voters', ListVotersByWalletController::class)->name('wallet.voters');
Route::get('/wallets/{wallet}/blocks', ListBlocksByWalletController::class)->name('wallet.blocks');

Route::view('/statistics', 'app.statistics')->name('statistics');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('contact', [ContactController::class, 'handle']);
// ->middleware([
//     ProtectAgainstSpam::class,
//     'throttle:5,60',
// ]);

// Explorer 3.0 BC - Remove after some time!
Route::redirect('/advanced-search', '/search');
Route::redirect('/delegate-monitor', '/delegates');
Route::redirect('/top-wallets', '/wallets');
Route::get('/block/{block}', fn (Block $block) => redirect()->route('block', ['block' => $block]));
Route::get('/transaction/{transaction}', fn (Transaction $transaction) => redirect()->route('transaction', ['transaction' => $transaction]));
Route::get('/wallet/{wallet}', fn (Wallet $wallet) => redirect()->route('wallet', ['wallet' => $wallet]));
