<?php

declare(strict_types=1);

namespace App\Models\Composers;

use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

final class TimestampRangeComposer
{
    public static function compose(Builder $query, array $parameters): Builder
    {
        $from = Arr::get($parameters, 'dateFrom');
        $to   = Arr::get($parameters, 'dateTo');

        if (is_null($from) && is_null($to)) {
            return $query;
        }

        if (! is_null($from)) {
            $from = Timestamp::fromUnix(Carbon::parse($from)->unix())->unix();

            $query->where('timestamp', '>=', $from);
        }

        if (! is_null($to)) {
            $to = Timestamp::fromUnix(Carbon::parse($to)->unix())->unix();

            $query->where('timestamp', '<=', $to);
        }

        return $query;
    }
}
