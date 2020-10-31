<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Round;
use Illuminate\Support\Collection;

interface RoundRepository
{
    public function allByRound(int $round): Collection;

    public function currentRound(): Round;
}
