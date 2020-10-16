<?php

declare(strict_types=1);

use App\Http\Controllers\ListBlocksByWalletController;
use App\Http\Controllers\ListBlocksController;
use App\Http\Controllers\ListTransactionsByBlockController;
use App\Http\Controllers\ListTransactionsByWalletController;
use App\Http\Controllers\ListTransactionsController;
use App\Http\Controllers\ListVotersByWalletController;
use App\Http\Controllers\ShowBlockController;
use App\Http\Controllers\ShowDelegateMonitorController;
use App\Http\Controllers\ShowTopWalletsController;
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

Route::view('/', 'app.home')->name('home');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/wallets/{wallet}', ShowWalletController::class);
Route::get('/wallets/{wallet}/voters', ListVotersByWalletController::class);
Route::get('/wallets/{wallet}/blocks', ListBlocksByWalletController::class);
Route::get('/wallets/{wallet}/transactions', ListTransactionsByWalletController::class);

Route::get('/blocks', ListBlocksController::class);
Route::get('/blocks/{block}', ShowBlockController::class);
Route::get('/blocks/{block}/transactions', ListTransactionsByBlockController::class);

Route::get('/transactions', ListTransactionsController::class);
Route::get('/transactions/{transaction}', ShowTransactionController::class);

Route::get('/delegate-monitor', ShowDelegateMonitorController::class);
Route::get('/top-wallets', ShowTopWalletsController::class);
