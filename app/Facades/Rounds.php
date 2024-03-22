<?php

declare(strict_types=1);

namespace App\Facades;

use App\Contracts\RoundRepository;
use App\Models\Round;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Round byRound(int $round)
 * @method static Round current()
 * @method static SupportCollection delegates()
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
