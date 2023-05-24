<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Exchange extends Model
{
    use HasFactory;

    public function scopeCoingecko(Builder $query): Builder
    {
        return $query->whereNotNull('coingecko_id');
    }
}
