<?php

declare(strict_types=1);

namespace App\Providers;

use App\View\Components\TableSkeleton;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

final class BladeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::component('table-skeleton', TableSkeleton::class);
    }
}
