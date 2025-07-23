<?php

declare(strict_types=1);

namespace App\Http\Livewire\Wallet;

use App\Facades\Wallets;
use App\Http\Livewire\Abstracts\TabbedComponent;
use App\Http\Livewire\Wallet\Concerns\BlocksTab;
use App\Http\Livewire\Wallet\Concerns\TransactionsTab;
use App\Http\Livewire\Wallet\Concerns\VotersTab;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;

/**
 * @property int $page
 * @property ?int $perPage
 */
final class Tabs extends TabbedComponent
{
    use BlocksTab;
    use TransactionsTab;
    use VotersTab;

    public const INITIAL_VIEW = 'transactions';

    public const INITIAL_FILTERS = [
        'transactions' => [
            'outgoing'            => true,
            'incoming'            => true,
            'transfers'           => true,
            'multipayments'       => true,
            'votes'               => true,
            'validator'           => true,
            'username'            => true,
            'contract_deployment' => true,
            'others'              => true,
        ],
    ];

    public string $view = 'transactions';

    public string $previousView = 'transactions';

    public string $address;

    public array $alreadyLoadedViews = [
        'transactions' => false,
        'blocks'       => false,
        'voters'       => false,
    ];

    public function getListeners(): array
    {
        return [
            'currencyChanged' => '$refresh',
        ];
    }

    public function mount(): void
    {
        parent::mount();

        if ($this->tabQueryData === []) {
            $this->tabQueryData = [
                'transactions' => [
                    'paginators.transactions'        => $this->paginators['transactions'],
                    'paginatorsPerPage.transactions' => $this->paginatorsPerPage['transactions'],

                    'filters.transactions.outgoing' => $this->filters['transactions']['outgoing'],
                    'filters.transactions.incoming' => $this->filters['transactions']['incoming'],
                    'filters.transactions.transfers' => $this->filters['transactions']['transfers'],
                    'filters.transactions.multipayments' => $this->filters['transactions']['multipayments'],
                    'filters.transactions.votes' => $this->filters['transactions']['votes'],
                    'filters.transactions.validator' => $this->filters['transactions']['validator'],
                    'filters.transactions.username' => $this->filters['transactions']['username'],
                    'filters.transactions.contract_deployment' => $this->filters['transactions']['contract_deployment'],
                    'filters.transactions.others' => $this->filters['transactions']['others'],
                ],

                'blocks' => [
                    'paginators.blocks'        => $this->paginators['blocks'],
                    'paginatorsPerPage.blocks' => $this->paginatorsPerPage['blocks'],
                ],

                'voters' => [
                    'paginators.voters'        => $this->paginators['voters'],
                    'paginatorsPerPage.voters' => $this->paginatorsPerPage['voters'],
                ],
            ];
        }
    }

    public function render(): View
    {
        return view('livewire.wallet.tabs', [
            'wallet'       => ViewModelFactory::make(Wallets::findByAddress($this->address)),
            'blocks'       => ViewModelFactory::paginate($this->blocks),
            'transactions' => ViewModelFactory::paginate($this->transactions),
            'voters'       => ViewModelFactory::paginate($this->voters),
        ]);
    }

    #[On('showWalletView')]
    public function showWalletView(string $view): void
    {
        $this->syncInput('view', $view);
    }
}
