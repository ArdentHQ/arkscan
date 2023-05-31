<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Models\Transaction;
use App\Services\Search\BlockSearch;
use App\Services\Search\TransactionSearch;
use App\Services\Search\WalletSearch;
use App\ViewModels\ViewModelFactory;
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

        $results = (new WalletSearch())->search(query: $query, limit: 5);
        $results = $results->concat((new TransactionSearch())->search(query: $query, limit: 5));
        $results = $results->concat((new BlockSearch())->search(query: $query, limit: 5));

        return ViewModelFactory::collection($results);
    }
}
