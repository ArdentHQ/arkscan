<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;

final class ExchangeTableFilter extends Component
{
    public ?string $type = 'all';

    public ?string $pair = 'all';

    public $types = [
        'all'         => 'general.all',
        'exchanges'   => 'pages.exchanges.type.exchanges',
        'aggregators' => 'pages.exchanges.type.agreggators',
    ];

    public $pairs = [
        'all'         => 'general.all',
        'btc'         => 'pages.exchanges.pair.btc',
        'eth'         => 'pages.exchanges.pair.eth',
        'stablecoins' => 'pages.exchanges.pair.stablecoins',
        'other'       => 'pages.exchanges.pair.other',
    ];

    protected $queryString = [
        'type' => ['except' => 'all'],
        'pair' => ['except' => 'all'],
    ];

    public function render(): View
    {
        return view('livewire.exchange-table-filter', [
            'selectedType' => in_array($this->type, array_keys($this->types), true) ? $this->type : 'all',
            'selectedPair' => in_array($this->pair, array_keys($this->pairs), true) ? $this->pair : 'all',
        ]);
    }

    public function setFilter(string $param, string $value): void
    {
        $this->{$param} = $value;

        $this->emit('filterChanged', $param, $value);
    }
}
