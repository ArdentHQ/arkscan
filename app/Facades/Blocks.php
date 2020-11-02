<?php

declare(strict_types=1);

namespace App\Facades;

use App\Contracts\BlockRepository;
use App\Models\Block;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Block findByHeight(int $height)
 */
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
