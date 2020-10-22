<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Block;
use App\Services\Search\Concerns\FiltersDateRange;
use App\Services\Search\Concerns\FiltersValueRange;
use Illuminate\Database\Eloquent\Builder;

final class BlockSearch implements Search
{
    use FiltersDateRange;
    use FiltersValueRange;

    public function search(array $parameters): Builder
    {
        $query = Block::query();

        if ($parameters['term']) {
            $query = $query->where('id', $parameters['term']);
        }

        // $this->queryValueRange($query, $parameters['totalAmountFrom'], $parameters['totalAmountTo']);

        // $this->queryValueRange($query, $parameters['totalFeeFrom'], $parameters['totalFeeTo']);

        // $this->queryDateRange($query, $parameters['dateFrom'], $parameters['dateTo']);

        // if ($parameters['generatorPublicKey']) {
        //     $query->where('generator_public_key', $parameters['generatorPublicKey']);
        // }

        return $query;
    }
}
