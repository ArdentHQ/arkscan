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

    /**
     * @var mixed
     */
    protected $queryString = [
        'type' => ['except' => 'all'],
        'pair' => ['except' => 'all'],
    ];

    /**
     * @var mixed
     */
    protected $listeners = [
        'filterChanged'   => 'setFilter',
        'currencyChanged' => '$refresh',
    ];

    public function render(): View
    {
        return view('livewire.exchange-table', [
            'exchanges' => $this->getExchanges(),
        ]);
    }

    public function getExchanges(): Collection
    {
        return Exchange::filterByType($this->type)
            ->filterByPair($this->pair)
            ->get()
            ->sort(function ($a, $b) {
                if ($b->volume === null) {
                    return -1;
                }

                if ($a->volume === null) {
                    return 1;
                }

                return intval($b->volume) - intval($a->volume);
            });
    }

    public function setFilter(string $param, string $value): void
    {
        if ($param === 'type') {
            $this->type = $value;
        }

        if ($param === 'pair') {
            $this->pair = $value;
        }
    }
}
