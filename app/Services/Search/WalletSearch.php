<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Wallet;
use App\Services\Search\Traits\ValidatesTerm;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class WalletSearch implements Search
{
    use ValidatesTerm;

    public function search(string $query, int $limit): Collection
    {
        if ($this->couldntBeAddress($query)) {
            return collect();
        }

        if ($this->couldBeAddress($query)) {
            // Quoted so it gets the exact match
            $builder = Wallet::search(sprintf('"%s"', $query))->take($limit);
        } else {
            $builder = Wallet::search($query)->take($limit);
        }

        return collect($builder->raw()['hits'])->map(function ($item) {
            return new Wallet([
                ...$item,
                'attributes' => [
                    'delegate' => [
                        'username' => Arr::get($item, 'username'),
                    ],
                ],
            ]);
        });
    }
}
