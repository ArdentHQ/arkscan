<?php

declare(strict_types=1);

namespace App\Http\Livewire\Wallet;

use App\Facades\Wallets;
use App\Http\Livewire\Abstracts\TabbedComponent;
use App\Http\Livewire\Concerns\HasTableFilter;
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
    use HasTableFilter;
    use BlocksTab;
    use TransactionsTab;
    use VotersTab;

    public const INITIAL_VIEW = 'transactions';

    public string $view = 'transactions';

    public string $previousView = 'transactions';

    public string $address;

    public array $alreadyLoadedViews = [
        'transactions' => false,
        'blocks'       => false,
        'voters'       => false,
    ];

    // public function queryString(): array
    // {
    //     $params = [
    //         'paginators.page' => ['except' => 1, 'as' => 'page'],
    //         'perPage'         => ['except' => intval(config('arkscan.pagination.per_page'))],
    //         'view'            => ['except' => 'transactions', 'history' => true],
    //     ];

    //     // We need to pass in the transaction filters for previous view so we can hide it from the URL
    //     if ($this->view !== 'transactions' && $this->previousView !== 'transactions') {
    //         return $params;
    //     }

    //     return [
    //         ...$params,

    //         // Transaction Filters
    //         'outgoing'      => ['except' => true, 'history' => true],
    //         'incoming'      => ['except' => true, 'history' => true],
    //         'transfers'     => ['except' => true, 'history' => true],
    //         'votes'         => ['except' => true, 'history' => true],
    //         'others'        => ['except' => true, 'history' => true],
    //     ];
    // }

    public function getListeners(): array
    {
        return [
            'currencyChanged' => '$refresh',
        ];
    }

    public function mount(): void
    {
        parent::mount();

        if (count($this->filters) === 0) {
            $this->filters = [
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
        }

        if ($this->tabQueryData === []) {
            $this->tabQueryData = [
                'transactions' => [
                    'paginators.transactions'        => $this->paginators['transactions'],
                    'paginatorsPerPage.transactions' => $this->paginatorsPerPage['transactions'],

                    // TODO: Filters - https://app.clickup.com/t/86dvxzge7
                ],

                'blocks' => [
                    'paginators.blocks'        => $this->paginators['blocks'],
                    'paginatorsPerPage.blocks' => $this->paginatorsPerPage['blocks'],
                ],

                'voters' => [
                    'paginators.voters'        => $this->paginators['voters'],
                    'paginatorsPerPage.voters' => $this->paginatorsPerPage['voters'],

                    // TODO: Filters - https://app.clickup.com/t/86dvxzge7
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
