<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Wallet;
use App\Services\Search\Concerns\FiltersDateRange;
use App\Services\Search\Concerns\FiltersValueRange;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

final class WalletSearch implements Search
{
    use FiltersDateRange;
    use FiltersValueRange;

    public function search(array $parameters): Builder
    {
        $query = Wallet::query();

        $this->queryValueRange($query, 'balance', Arr::get($parameters, 'balanceFrom', 0), Arr::get($parameters, 'balanceTo', 0));

        if (! is_null(Arr::get($parameters, 'term'))) {
            $query->where('address', $parameters['term']);
            $query->orWhere('public_key', $parameters['term']);
        }

        if (! is_null(Arr::get($parameters, 'username'))) {
            $query->where('attributes->delegate->username', $parameters['username']);
        }

        if (! is_null(Arr::get($parameters, 'vote'))) {
            $query->where('attributes->vote', $parameters['vote']);
        }

        return $query;
    }
}
