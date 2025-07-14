<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Wallets;
use App\Http\Livewire\Concerns\HasTabs;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * @property int $page
 * @property ?int $perPage
 */
final class WalletTables extends Component
{
    use HasTabs;

    public string $address;

    public string $view = 'transactions';

    public string $previousView = 'transactions';

    public array $alreadyLoadedViews = [
        'transactions' => false,
        'blocks'       => false,
        'voters'       => false,
    ];

    public function queryString(): array
    {
        return [
            'paginators.page' => ['except' => 1, 'history' => true],
            'perPage'         => ['except' => intval(config('arkscan.pagination.per_page'))],
            'view'            => ['except' => 'transactions', 'history' => true],

            // Transaction Filters
            'outgoing'      => ['except' => true, 'history' => true],
            'incoming'      => ['except' => true, 'history' => true],
            'transfers'     => ['except' => true, 'history' => true],
            'votes'         => ['except' => true, 'history' => true],
            'others'        => ['except' => true, 'history' => true],
        ];
    }

    public function mount(WalletViewModel $wallet): void
    {
        $this->address = $wallet->address();

        if ($this->tabQueryData === []) {
            $this->tabQueryData = [
                'transactions' => [
                    'paginators.page' => 1,
                    'perPage'         => WalletTransactionTable::defaultPerPage(),
                    'outgoing'        => true,
                    'incoming'        => true,
                    'transfers'       => true,
                    'votes'           => true,
                    'others'          => true,
                ],

                'blocks' => [
                    'paginators.page' => 1,
                    'perPage'         => WalletBlockTable::defaultPerPage(),
                ],

                'voters' => [
                    'paginators.page' => 1,
                    'perPage'         => WalletVoterTable::defaultPerPage(),
                ],
            ];

            $view = $this->resolveView();
            if (! array_key_exists($view, $this->tabQueryData)) {
                return;
            }

            $this->gotoPage($this->resolvePage(), false);
            $this->setPerPage($this->resolvePerPage());
        }
    }

    public function render(): View
    {
        return view('livewire.wallet-tables', [
            'wallet' => ViewModelFactory::make(Wallets::findByAddress($this->address)),
        ]);
    }

    #[On('showWalletView')]
    public function showWalletView(string $view): void
    {
        $this->syncInput('view', $view);
    }

    private function tabbedComponent(): string
    {
        return [
            'transactions' => WalletTransactionTable::class,
            'blocks'       => WalletBlockTable::class,
            'voters'       => WalletVoterTable::class,
        ][$this->view];
    }
}
