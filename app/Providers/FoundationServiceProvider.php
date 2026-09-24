<?php

declare(strict_types=1);

namespace App\Providers;

use App\Testing\ParallelTestingServiceProvider;
use Illuminate\Foundation\Providers\FormRequestServiceProvider;
use Illuminate\Foundation\Providers\FoundationServiceProvider as Base;

final class FoundationServiceProvider extends Base
{
    /**
     * The provider class names.
     *
     * @var array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected $providers = [
        FormRequestServiceProvider::class,
        ParallelTestingServiceProvider::class,
    ];
}
