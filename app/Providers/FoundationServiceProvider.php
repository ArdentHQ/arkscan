<?php

namespace App\Providers;

use App\Testing\ParallelTestingServiceProvider;
use Illuminate\Foundation\Providers\FormRequestServiceProvider;
use Illuminate\Foundation\Providers\FoundationServiceProvider as Base;

class FoundationServiceProvider extends Base
{
    /**
     * The provider class names.
     *
     * @var string[]
     */
    protected $providers = [
        FormRequestServiceProvider::class,
        ParallelTestingServiceProvider::class,
    ];
}
