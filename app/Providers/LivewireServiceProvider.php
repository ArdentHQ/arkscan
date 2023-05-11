<?php

namespace App\Providers;

use App\Livewire\Controllers\HttpConnectionHandler;
use Illuminate\Support\Facades\Route;
use Livewire\LivewireServiceProvider as Base;

class LivewireServiceProvider extends Base
{
    protected function registerRoutes()
    {
        Route::post('/livewire/message/{name}', HttpConnectionHandler::class)
            ->name('livewire.message')
            ->middleware(config('livewire.middleware_group', ''));
    }
}
