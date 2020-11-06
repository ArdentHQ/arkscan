<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Composers\ValueRangeComposer;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

final class WalletSearch implements Search
{
    public function search(array $parameters): Builder
    {
        $query = Wallet::query();

        ValueRangeComposer::compose($query, $parameters, 'balance');

        if (! is_null(Arr::get($parameters, 'term'))) {
            $query->where('address', $parameters['term']);
            $query->orWhere('public_key', $parameters['term']);
            $query->orWhere('attributes->delegate->username', $parameters['term']);
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
