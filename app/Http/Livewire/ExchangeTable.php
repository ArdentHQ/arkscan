<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Exchange;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

final class ExchangeTable extends Component
{
    public ?string $type = 'all';

    public ?string $pair = 'all';

    protected $queryString = [
        'type' => ['except' => 'all'],
        'pair' => ['except' => 'all'],
    ];

    protected $listeners = ['filterChanged' => 'setFilter'];

    public function render(): View
    {
        return view('livewire.exchange-table', [
            'exchanges' => $this->getExchanges(),
        ]);
    }

    public function getExchanges(): Collection
    {
        return Exchange::orderBy('name')
            ->filterByType($this->type)
            ->filterByPair($this->pair)
            ->get();
    }

    public function setFilter(string $param, string $value): void
    {
        $this->{$param} = $value;
    }
}
