<?php

namespace App\Http\Livewire\Navbar;

use App\Services\Search\BlockSearch;
use App\Services\Search\TransactionSearch;
use App\Services\Search\WalletSearch;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator as PaginationLengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Search extends Component
{
    public ?string $query = '68be9f9ec22117aaa52f2cf50abcce2a0edcbdf44ca3b28e49e4466e898d9621';
    // public ?string $query = '32232d59e0110835943527c87f0e749c62b5b4235dc35e2ce24f8d5add141cb1';
    // public ?string $query = 'D62h47qvxjxWMw2aJf74iN6LG2o3nmb3We';

    public bool $hasResults = false;

    protected array $rules = [
        'query' => [
            'required', 'string', 'max:66',
        ],
    ];

    public function render(): View
    {
        return view('livewire.navbar.search', [
            'results' => $this->results(),
        ]);
    }

    public function clear(): void
    {
        $this->query = null;
    }

    public function setHasResults(?LengthAwarePaginator $results = null): void
    {
        $this->hasResults = $results !== null && $results->isNotEmpty();
    }

    public function results(): ?LengthAwarePaginator
    {
        $validator = Validator::make([
            'query' => $this->query,
        ], $this->rules);

        if ($validator->fails()) {
            $this->setHasResults();

            return new PaginationLengthAwarePaginator([], 0, 3);
        }

        $data = $validator->validate();

        $results = (new WalletSearch())->search(['term' => Arr::get($data, 'query')])->paginate();

        if ($results->isEmpty()) {
            $results = (new TransactionSearch())->search(['term' => Arr::get($data, 'query')])->paginate();
        }

        if ($results->isEmpty()) {
            $results = (new BlockSearch())->search(['term' => Arr::get($data, 'query')])->paginate();
        }

        $this->setHasResults($results);

        return ViewModelFactory::paginate($results);
    }
}
