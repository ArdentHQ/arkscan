<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Composers\ValueRangeComposer;
use App\Models\Wallet;
use App\Services\Search\Traits\ValidatesTerm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

final class WalletSearch implements Search
{
    use ValidatesTerm;

    public function search(array $parameters): Builder
    {
        $query = Wallet::query();

        ValueRangeComposer::compose($query, $parameters, 'balance');

        $term = Arr::get($parameters, 'term');

        if (! is_null($term)) {
            if ($this->couldBeAddress($term)) {
                $query->whereLower('address', $term);
            } elseif ($this->couldBePublicKey($term)) {
                $query->whereLower('public_key', $term);
            } elseif ($this->couldBeUsername($term)) {
                $username = substr(DB::getPdo()->quote($term), 1, -1);
                $query->whereRaw('lower(attributes::text)::jsonb @> lower(\'{"delegate":{"username":"'.$username.'"}}\')::jsonb');
            } else {
                // Empty results when it has a term but not possible results
                $query->empty();
            }
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
