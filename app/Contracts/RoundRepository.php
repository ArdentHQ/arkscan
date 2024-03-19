<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Round;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface RoundRepository
{
    /**
     * @param int $round
     * @return Collection<Round>
     */
    public function allByRound(int $round): Collection;

    public function current(): int;

    public function delegates(bool $withBlock = true): SupportCollection;
}
