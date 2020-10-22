<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Wallet;
use App\Services\Search\Concerns\FiltersDateRange;
use App\Services\Search\Concerns\FiltersValueRange;
use Illuminate\Database\Eloquent\Builder;

final class WalletSearch implements Search
{
    use FiltersDateRange;
    use FiltersValueRange;

    public function search(array $parameters): Builder
    {
        $query = Wallet::query();

        $this->queryValueRange($query, $parameters['balanceFrom'], $parameters['balanceTo']);

        if ($parameters['term']) {
            $query->where('address', $parameters['term']);
            $query->orWhere('public_key', $parameters['term']);
        }

        if ($parameters['username']) {
            $query->where('username', $parameters['username']);
        }

        if ($parameters['vote']) {
            $query->where('vote', $parameters['vote']);
        }

        return $query;
    }
}
