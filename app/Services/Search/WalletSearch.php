<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Composers\ValueRangeComposer;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

final class WalletSearch implements Search
{
    public function search(array $parameters): Builder
    {
        $query = Wallet::query();

        ValueRangeComposer::compose($query, $parameters, 'balance');

        if (! is_null(Arr::get($parameters, 'term'))) {
            $query->whereLower('address', $parameters['term']);
            $query->orWhereLower('public_key', $parameters['term']);

            $username = substr(DB::getPdo()->quote($parameters['term']), 1, -1);
            $query->orWhereRaw('lower(attributes::text)::jsonb @> lower(\'{"delegate":{"username":"'.$username.'"}}\')::jsonb');
        }

        if (! is_null(Arr::get($parameters, 'username'))) {
            $username = substr(DB::getPdo()->quote($parameters['username']), 1, -1);
            $query->orWhereRaw('lower(attributes::text)::jsonb @> lower(\'{"delegate":{"username":"'.$username.'"}}\')::jsonb');
        }

        if (! is_null(Arr::get($parameters, 'vote'))) {
            $vote = substr(DB::getPdo()->quote($parameters['vote']), 1, -1);
            $query->orWhereRaw('lower(attributes::text)::jsonb @> lower(\'{"vote":"'.$vote.'"}\')::jsonb');
        }

        return $query;
    }
}
