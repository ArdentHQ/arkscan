<?php

declare(strict_types=1);

namespace App\Providers;

use Livewire\LivewireServiceProvider as Base;

final class LivewireServiceProvider extends Base
{
    // TODO - extend $handle to "handle" livewire requests on 404 error pages - https://app.clickup.com/t/86dtq95zn
    // protected function boot(): void
    // {
    //     Livewire::setUpdateRoute(function ($handle) {
    //         return Route::post('/custom/livewire/update', $handle);
    //     });
    // }
}
