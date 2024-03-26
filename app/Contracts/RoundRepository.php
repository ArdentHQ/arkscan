<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Round;
use Illuminate\Support\Collection as SupportCollection;

interface RoundRepository
{
    public function current(): Round;

    public function byRound(int $round): Round;

    public function validators(bool $withBlock = true): SupportCollection;
}
