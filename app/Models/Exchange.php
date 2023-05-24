<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Exchange extends Model
{
    use HasFactory;

    public function scopeFilterByType(Builder $query, string $type)
    {
        if ($type === 'exchanges') {
            return $query->where('is_exchange', true);
        }

        if ($type === 'aggregators') {
            return $query->where('is_aggregator', true);
        }

        return $query;
    }

    public function scopeFilterByPair(Builder $query, string $pair): Builder
    {
        if ($pair === 'btc') {
            return $query->where('btc', true);
        }

        if ($pair === 'eth') {
            return $query->where('eth', true);
        }

        if ($pair === 'stablecoins') {
            return $query->where('stablecoins', true);
        }
        
        if ($pair === 'other') {
            return $query->where('other', true);
        }

        return $query;
    }
}
