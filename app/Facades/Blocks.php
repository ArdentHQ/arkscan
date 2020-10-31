<?php

declare(strict_types=1);

namespace App\Facades;

use App\Contracts\BlockRepository;
use Illuminate\Support\Facades\Facade;

final class Blocks extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BlockRepository::class;
    }
}
