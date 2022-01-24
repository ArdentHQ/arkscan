<?php

declare(strict_types=1);

namespace App\Facades;

use App\Contracts\RoundRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection allByRound(int $round)
 * @method static int current()
 */
final class Rounds extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return RoundRepository::class;
    }
}
