<?php

declare(strict_types=1);

namespace App\Events\Statistics;

use App\Events\NewEntity;

abstract class StatisticsUpdate extends NewEntity
{
    public const CHANNEL = 'statistics-update';
}
