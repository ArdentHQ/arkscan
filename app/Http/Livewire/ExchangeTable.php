<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Scopes\OrderByBalanceScope;
use App\Models\Wallet;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Component;

final class ExchangeTable extends Component
{
    use HasPagination;

    public function render(): View
    {
        return view('livewire.exchange-table', [
            // TODO: use actual data
            'exchanges' => $this->exchanges,
        ]);
    }

    public function getExchangesProperty(): LengthAwarePaginator
    {
        return new LengthAwarePaginator([
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
        ], 4, 10);
    }
}
