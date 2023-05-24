<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Exchange;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

final class ExchangeTableFilter extends Component
{
    public ?string $type = 'all';
    
    public ?string $pair = 'all';

    protected $queryString = [
        'type' => ['except' => 'all'],
        'pair' => ['except' => 'all'],
    ];

    public $types = [
        'all' => 'general.all',
        'exchanges' => 'pages.exchanges.type.exchanges',
        'aggregators' => 'pages.exchanges.type.agreggators',
    ];

    public $pairs = [
        'all' => 'general.all',
        'btc' => 'pages.exchanges.pair.btc',
        'eth' => 'pages.exchanges.pair.eth',
        'stablecoins' => 'pages.exchanges.pair.stablecoins',
    ];

    public function render(): View
    {
        return view('livewire.exchange-table-filter', [
            'selectedType' => in_array($this->type, array_keys($this->types)) ? $this->type : 'all',
            'selectedPair' => in_array($this->pair, array_keys($this->pairs)) ? $this->pair : 'all',
        ]);
    }

    public function setFilter(string $param, string $value): void
    {
        $this->{$param} = $value;
    }
}
