<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Exchange extends Model
{
    use HasFactory;

    public function scopeFilterByType(Builder $query, ?string $type): Builder
    {
        return $query
            ->when($type === 'exchanges', static fn ($query) => $query->where('is_exchange', true))
            ->when($type === 'aggregators', static fn ($query) => $query->where('is_aggregator', true));
    }

    public function scopeFilterByPair(Builder $query, ?string $pair): Builder
    {
        if (in_array($pair, ['btc', 'eth', 'stablecoins', 'other'], true)) {
            return $query->where($pair, true);
        }

        return $query;
    }
}
