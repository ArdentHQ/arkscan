<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Block;
use App\Services\Search\Concerns\FiltersDateRange;
use App\Services\Search\Concerns\FiltersValueRange;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

final class BlockSearch implements Search
{
    use FiltersDateRange;
    use FiltersValueRange;

    public function search(array $parameters): Builder
    {
        $query = Block::query();

        if (! is_null(Arr::get($parameters, 'term'))) {
            $query = $query->where('id', $parameters['term']);
        }

        $this->queryValueRange($query, 'height', Arr::get($parameters, 'heightFrom'), Arr::get($parameters, 'heightTo'));

        $this->queryValueRange($query, 'total_amount', Arr::get($parameters, 'totalAmountFrom'), Arr::get($parameters, 'totalAmountTo'));

        $this->queryValueRange($query, 'total_fee', Arr::get($parameters, 'totalFeeFrom'), Arr::get($parameters, 'totalFeeTo'));

        $this->queryDateRange($query, Arr::get($parameters, 'dateFrom'), Arr::get($parameters, 'dateTo'));

        if (! is_null(Arr::get($parameters, 'generatorPublicKey'))) {
            $query->where('generator_public_key', $parameters['generatorPublicKey']);
        }

        return $query;
    }
}
