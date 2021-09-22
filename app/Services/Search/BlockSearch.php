<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Facades\Wallets;
use App\Models\Block;
use App\Models\Composers\TimestampRangeComposer;
use App\Models\Composers\ValueRangeComposer;
use App\Services\Search\Traits\ValidatesTerm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Throwable;

final class BlockSearch implements Search
{
    use ValidatesTerm;

    public function search(array $parameters): Builder
    {
        $query = Block::query();

        $this->applyScopes($query, $parameters);

        $term = Arr::get($parameters, 'term');

        if (! is_null($term)) {
            if ($this->couldBeBlockID($term)) {
                $query = $query->whereLower('id', $term);
            } else {
                // Forces empty results when it has a term but not possible
                // block ID
                $query->empty();
            }

            if ($this->couldBeHeightValue($term)) {
                $numericTerm = strval(filter_var($term, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND));
                $query->orWhere('height', $numericTerm);
            }

            try {
                // If there is a term we also want to check if the term is a valid wallet.
                $query->orWhere(function ($query) use ($parameters, $term): void {
                    $wallet = Wallets::findByIdentifier($term);

                    $query->whereLower('generator_public_key', $wallet->public_key);

                    $this->applyScopes($query, $parameters);
                });
            } catch (Throwable) {
                // If this throws then the term was not a valid address, public key or username.
            }
        }

        return $query;
    }

    private function applyScopes(Builder $query, array $parameters): void
    {
        ValueRangeComposer::compose($query, $parameters, 'height', false);

        ValueRangeComposer::compose($query, $parameters, 'total_amount');

        ValueRangeComposer::compose($query, $parameters, 'total_fee');

        ValueRangeComposer::compose($query, $parameters, 'reward');

        TimestampRangeComposer::compose($query, $parameters);

        if (! is_null(Arr::get($parameters, 'generatorPublicKey'))) {
            $query->whereLower('generator_public_key', $parameters['generatorPublicKey']);
        }
    }
}
