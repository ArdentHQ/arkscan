<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Search\BlockSearch;
use App\Services\Search\TransactionSearch;
use App\Services\Search\WalletSearch;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as PaginationLengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

trait ManagesSearch
{
    public ?string $query = null;

    protected array $rules = [
        'query' => [
            'required', 'string', 'max:66',
        ],
    ];

    public function clear(): void
    {
        $this->query = null;
    }

    public function results(): ?Collection
    {
        $validator = Validator::make([
            'query' => $this->query,
        ], $this->rules);

        if ($validator->fails()) {
            return new Collection();
        }

        $data = $validator->validate();

        $query = Arr::get($data, 'query');

        if ($builder = (new WalletSearch())->search($query)) {
            $results = $builder->take(5)->get();
        } else {
            $results = new Collection();
        }

        $results = $results->concat((new TransactionSearch())->search($query)->take(5)->get());

        $results = $results->concat((new BlockSearch())->search($query)->take(5)->get());

        return ViewModelFactory::collection($results);
    }
}
