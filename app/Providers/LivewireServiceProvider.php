<?php

declare(strict_types=1);

namespace App\Providers;

use App\Livewire\Controllers\HttpConnectionHandler;
use Illuminate\Support\Facades\Route;
use Livewire\LivewireServiceProvider as Base;

final class LivewireServiceProvider extends Base
{
    // protected function registerRoutes(): void
    // {
    //     Route::post('/livewire/message/{name}', HttpConnectionHandler::class)
    //         ->name('livewire.message')
    //         ->middleware(config('livewire.middleware_group', ''));
    // }
}
