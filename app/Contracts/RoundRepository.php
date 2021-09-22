<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Support\Collection;

interface RoundRepository
{
    public function allByRound(int $round): Collection;

    public function current(): int;
}
