<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Wallets;
use App\Http\Livewire\Concerns\HasTabs;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * @property int $page
 * @property int $perPage
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

            $view = $this->resolveView();
            if (! array_key_exists($view, $this->tabQueryData)) {
                return;
            }

            $this->tabQueryData[$view]['page'] = $this->resolvePage();

            $this->tabQueryData[$view]['perPage'] = $this->resolvePerPage();
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
