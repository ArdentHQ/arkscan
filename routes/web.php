<?php

declare(strict_types=1);

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListBlocksByWalletController;
use App\Http\Controllers\ListVotersByWalletController;
use App\Http\Controllers\ShowBlockController;
use App\Http\Controllers\ShowTransactionController;
use App\Http\Controllers\ShowWalletController;
use App\Http\Controllers\TransactionsController;
use Illuminate\Support\Facades\Route;

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

// Explorer 3.0 BC - Remove after some time!
Route::redirect('/advanced-search', '/search');
Route::redirect('/block/{blockId}', '/blocks/{blockId}');
Route::redirect('/delegate-monitor', '/delegates');
Route::redirect('/top-wallets', '/wallets');
Route::redirect('/transaction/{transactionId}', '/transactions/{transactionId}');
Route::redirect('/wallet/{walletId}', '/wallets/{walletId}');
