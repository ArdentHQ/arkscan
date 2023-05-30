<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Models\Wallet;
use App\Services\Search\BlockSearch;
use App\Services\Search\TransactionSearch;
use App\Services\Search\WalletSearch;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as PaginationLengthAwarePaginator;
use Illuminate\Support\Arr;
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

    public function results(): ?LengthAwarePaginator
    {
        $validator = Validator::make([
            'query' => $this->query,
        ], $this->rules);

        if ($validator->fails()) {
            return new PaginationLengthAwarePaginator([], 0, 3);
        }

        $data = $validator->validate();

        $query = Arr::get($data, 'query');

        $results = Wallet::search($query)->paginate(5);

        // $results = (new WalletSearch())->search(['term' => Arr::get($data, 'query')])->paginate();

        // if ($results->isEmpty()) {
        //     $results = (new TransactionSearch())->search(['term' => Arr::get($data, 'query')])->paginate();
        // }

        // if ($results->isEmpty()) {
        //     $results = (new BlockSearch())->search(['term' => Arr::get($data, 'query')])->paginate();
        // }

        return ViewModelFactory::paginate($results);
    }
}
