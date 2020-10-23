<?php

declare(strict_types=1);

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListBlocksByWalletController;
use App\Http\Controllers\ListBlocksController;
use App\Http\Controllers\ListTransactionsByBlockController;
use App\Http\Controllers\ListTransactionsByWalletController;
use App\Http\Controllers\ListTransactionsController;
use App\Http\Controllers\ListVotersByWalletController;
use App\Http\Controllers\ShowBlockController;
use App\Http\Controllers\ShowDelegateMonitorController;
use App\Http\Controllers\ShowTransactionController;
use App\Http\Controllers\ShowWalletController;
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
// TODO: Remove once /blocks is implemented
Route::view('/block', 'app.block')->name('block');
Route::view('/search', 'app.search-results')->name('search');

Route::view('/wallets', 'app.wallets')->name('wallets');
Route::get('/wallets/{wallet:address}', ShowWalletController::class)->name('wallet');
Route::get('/wallets/{wallet:address}/voters', ListVotersByWalletController::class)->name('wallet.voters');
Route::get('/wallets/{wallet:address}/blocks', ListBlocksByWalletController::class)->name('wallet.blocks');
Route::get('/wallets/{wallet:address}/transactions', ListTransactionsByWalletController::class)->name('wallet.transactions');

Route::get('/blocks', ListBlocksController::class)->name('blocks');
Route::get('/blocks/{block}', ShowBlockController::class)->name('block');
Route::get('/blocks/{block}/transactions', ListTransactionsByBlockController::class)->name('block.transactions');

Route::get('/transactions', ListTransactionsController::class)->name('transactions');
Route::get('/transactions/{transaction}', ShowTransactionController::class)->name('transaction');

Route::get('/delegate-monitor', ShowDelegateMonitorController::class)->name('monitor');
