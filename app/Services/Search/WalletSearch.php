<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Wallet;
use App\Services\Search\Traits\ValidatesTerm;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

final class WalletSearch implements Search
{
    use ValidatesTerm;

    /**
     * @return EloquentCollection<Wallet>
     */
    public function search(string $query, int $limit): EloquentCollection
    {
        if ($this->couldntBeAddress($query)) {
            return collect();
        }

        if ($this->couldBeAddress($query)) {
            $builder = Wallet::where('address', $query)->limit(1);
        } else {
            $builder = Wallet::where('address', 'ilike', sprintf('%%%s%%', $query))->limit($limit);
        }

        return $builder->get();
    }

    public static function mapMeilisearchResults(array $rawResults): Collection
    {
        return collect($rawResults)->map(fn ($item) => new Wallet([
            ...$item,
            'attributes' => [
                'delegate' => [
                    'username' => Arr::get($item, 'username'),
                ],
            ],
        ]));
    }
}
