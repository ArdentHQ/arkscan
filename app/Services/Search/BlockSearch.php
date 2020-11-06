<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Facades\Wallets;
use App\Models\Block;
use App\Models\Composers\TimestampRangeComposer;
use App\Models\Composers\ValueRangeComposer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

final class BlockSearch implements Search
{
    public function search(array $parameters): Builder
    {
        $query = Block::query();

        if (! is_null(Arr::get($parameters, 'term'))) {
            $query = $query->where('id', $parameters['term']);

            try {
                // If there is a term we also want to check if the term is a valid wallet.
                $query->orWhere(function ($query) use ($parameters): void {
                    $wallet = Wallets::findByIdentifier($parameters['term']);

                    $query->where('generator_public_key', $wallet->public_key);
                });
            } catch (\Throwable $th) {
                // If this throws then the term was not a valid address, public key or username.
            }
        }

        ValueRangeComposer::compose($query, $parameters, 'height', false);

        ValueRangeComposer::compose($query, $parameters, 'total_amount');

        ValueRangeComposer::compose($query, $parameters, 'total_fee');

        TimestampRangeComposer::compose($query, $parameters);

        if (! is_null(Arr::get($parameters, 'generatorPublicKey'))) {
            $query->where('generator_public_key', $parameters['generatorPublicKey']);
        }

        return $query;
    }
}
