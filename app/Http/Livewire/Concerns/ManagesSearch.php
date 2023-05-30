<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
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

        $results = Wallet::search($query)->get();

        $results = $results->concat(Transaction::search($query)->get());

        $results = $results->concat(Block::search($query)->get());

        return ViewModelFactory::collection($results);
    }
}
