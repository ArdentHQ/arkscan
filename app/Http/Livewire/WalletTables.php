<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Wallets;
use App\Http\Livewire\Concerns\HasTabs;
use App\Http\Livewire\Concerns\SyncsInput;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportLifecycleHooks\SupportLifecycleHooks;

/**
 * @property int $page
 * @property int $perPage
 */
final class WalletTables extends Component
{
    use HasTabs;
    use SyncsInput;

    public string $address;

    #[Url(except: 'transactions')]
    public string $view = 'transactions';

    public string $previousView = 'transactions';

    public array $alreadyLoadedViews = [
        'transactions' => false,
        'blocks'       => false,
        'voters'       => false,
    ];

    public function queryString(): array
    {
        $params = [
            'paginators.page' => ['except' => 1, 'as' => 'page'],
            'perPage'         => ['except' => intval(config('arkscan.pagination.per_page'))],
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

    // Constructor is used as Livewire seems to now try to manipulate query string properties before the first hook is called.
    public function __construct()
    {
        if ($this->tabQueryData === []) {
            $this->tabQueryData = [
                'transactions' => [
                    'paginators.page' => 1,
                    'perPage'         => WalletTransactionTable::defaultPerPage(),
                    'outgoing'        => true,
                    'incoming'        => true,
                    'transfers'       => true,
                    'votes'           => true,
                    'multipayments'   => true,
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

            $this->tabQueryData[$view]['paginators.page'] = $this->resolvePage();

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
