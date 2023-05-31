<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Services\Search\BlockSearch;
use App\Services\Search\TransactionSearch;
use App\Services\Search\WalletSearch;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as PaginationLengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

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

        $results = (new WalletSearch())->search(['term' => Arr::get($data, 'query')])->paginate();

        if ($results->isEmpty()) {
            $results = (new TransactionSearch())->search(['term' => Arr::get($data, 'query')])->paginate();
        }

        if ($results->isEmpty()) {
            $results = (new BlockSearch())->search(['term' => Arr::get($data, 'query')])->paginate();
        }

        return ViewModelFactory::paginate($results);
    }

    public function performSearch(): null|RedirectResponse|Redirector
    {
        /** @var null|PaginationLengthAwarePaginator $results */
        $results = $this->results();
        if ($results === null) {
            return null;
        }

        $result = $results->getCollection()->first();
        if ($result === null) {
            return null;
        }

        return redirect($result->url());
    }
}
