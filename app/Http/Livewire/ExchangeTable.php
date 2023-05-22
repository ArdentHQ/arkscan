<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

/**
 * @property Collection $exchanges
 */
final class ExchangeTable extends Component
{
    public function render(): View
    {
        return view('livewire.exchange-table', [
            // TODO: use actual data
            'exchanges' => $this->exchanges,
        ]);
    }

    public function getExchangesProperty(): Collection
    {
        return new Collection([
            [
                'name' => '7b',
                'icon' => 'app-exchanges.7b',
                'price' => '0.34100',
                'volume' => '2350503.97',
                'url' => 'https://7b.com',

                'pairs' => [
                    'BTC',
                    'ETH',
                    'Stablecoins',
                ],
            ],
            [
                'name' => '7b',
                'icon' => 'app-exchanges.7b',
                'price' => '0.34100',
                'volume' => '2350503.97',
                'url' => 'https://7b.com',

                'pairs' => [
                    'BTC',
                    'ETH',
                    'Stablecoins',
                ],
            ],
            [
                'name' => '7b',
                'icon' => 'app-exchanges.7b',
                'price' => null,
                'volume' => null,
                'url' => 'https://7b.com',

                'pairs' => [
                    'BTC',
                    'ETH',
                    'Stablecoins',
                ],
            ],
            [
                'name' => '7b',
                'icon' => 'app-exchanges.7b',
                'price' => '0.34100',
                'volume' => '2350503.97',
                'url' => 'https://7b.com',

                'pairs' => [
                    'BTC',
                    'ETH',
                    'Stablecoins',
                ],
            ],
        ]);
    }
}
