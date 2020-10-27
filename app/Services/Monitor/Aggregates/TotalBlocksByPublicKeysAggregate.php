<?php

declare(strict_types=1);

namespace App\Services\Monitor\Aggregates;

use App\Models\Block;

final class TotalBlocksByPublicKeysAggregate
{
    public function aggregate(array $publicKeys): array
    {
        return Block::query()
            ->whereIn('generator_public_key', $publicKeys)
            ->selectRaw('COUNT(*), generator_public_key')
            ->groupBy('generator_public_key')
            ->get()
            ->mapWithKeys(fn ($delegate) => [$delegate->generator_public_key => $delegate->count])
            ->toArray();
    }
}
