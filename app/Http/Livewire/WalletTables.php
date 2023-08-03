<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Wallets;
use App\Http\Livewire\Concerns\HasTabs;
use App\Livewire\SupportBrowserHistoryWrapper;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Livewire\Component;

final class WalletTables extends Component
{
    use HasTabs;

    public string $address;

    public string $view = 'transactions';

    public ?string $previousView = 'transactions';

    public array $tabQueryData = [];

    public array $savedQueryData = [];

    public array $alreadyLoadedViews = [
        'transactions' => false,
        'blocks'       => false,
        'voters'       => false,
    ];

    /** @var mixed */
    protected $listeners = [
        'showWalletView',
    ];

    public function queryString(): array
    {
        $params = [
            'view'    => ['except' => 'transactions'],
            'page'    => ['except' => 1],
            'perPage' => ['except' => intval(config('arkscan.pagination.per_page'))],
        ];

        // We need to pass in the transaction filters for previous view so we can hide it from the URL
        if ($this->view !== 'transactions' && $this->previousView !== 'transactions') {
            return $params;
        }

        return [
            ...$params,

            // Transaction Filters
            'outgoing'      => ['except' => true],
            'incoming'      => ['except' => true],
            'transfers'     => ['except' => true],
            'votes'         => ['except' => true],
            'multipayments' => ['except' => true],
            'others'        => ['except' => true],
        ];
    }

    public function boot(): void
    {
        if ($this->tabQueryData === []) {
            $this->tabQueryData = [
                'transactions' => [
                    'page'          => 1,
                    'perPage'       => WalletTransactionTable::defaultPerPage(),
                    'outgoing'      => true,
                    'incoming'      => true,
                    'transfers'     => true,
                    'votes'         => true,
                    'multipayments' => true,
                    'others'        => true,
                ],

                'blocks' => [
                    'page'    => 1,
                    'perPage' => WalletBlockTable::defaultPerPage(),
                ],

                'voters' => [
                    'page'    => 1,
                    'perPage' => WalletVoterTable::defaultPerPage(),
                ],
            ];
        }
    }

    public function mount(WalletViewModel $wallet): void
    {
        $this->address = $wallet->address();
    }

    public function render(): View
    {
        return view('livewire.wallet-tables', [
            'wallet' => ViewModelFactory::make(Wallets::findByAddress($this->address)),
        ]);
    }

    public function showWalletView(string $view): void
    {
        $this->view = $view;
    }
}
