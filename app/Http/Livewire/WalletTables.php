<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Wallets;
use App\Livewire\SupportBrowserHistoryWrapper;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Livewire\Component;
use Livewire\Features\SupportBrowserHistory;

final class WalletTables extends Component
{
    public string $address;

    public string $view = 'transactions';

    public ?string $previousView = 'transactions';

    public array $tabQueryData;

    /** @var mixed */
    protected $listeners = [
        'showWalletView',
    ];

    public function __get($property): mixed
    {
        $value = Arr::get($this->tabQueryData[$this->view], $property);
        if ($value !== null) {
            return $value;
        }

        $value = Arr::get($this->tabQueryData[$this->previousView], $property);
        if ($value !== null) {
            return $value;
        }

        return parent::__get($property);
    }

    public function __set($property, $value): void
    {
        if (Arr::has($this->tabQueryData[$this->view], $property)) {
            $this->tabQueryData[$this->view][$property] = $value;
        }

        if (Arr::has($this->tabQueryData[$this->previousView], $property)) {
            $this->tabQueryData[$this->previousView][$property] = $value;
        }
    }

    public array $savedQueryData = [];

    public function queryString(?string $view = null): array
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
        if (! isset($this->tabQueryData)) {
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

    public function updatingView(string $newView): void
    {
        if ($newView === $this->view) {
            return;
        }

        $this->previousView = $this->view;

        if (! array_key_exists($this->view, $this->tabQueryData)) {
            return;
        }

        SupportBrowserHistoryWrapper::init()->mergeRequestQueryStringWithComponent($this);

        $this->savedQueryData[$this->view] = $this->tabQueryData[$this->view];

        // Reset the querystring data on view change to clear the URL
        $queryStringData = $this->queryString();
        foreach ($this->tabQueryData[$this->view] as $key => $value) {
            if (! array_key_exists($key, $queryStringData)) {
                continue;
            }

            if (! array_key_exists('except', $queryStringData[$key])) {
                continue;
            }

            $this->{$key} = $queryStringData[$key]['except'];
        }
    }

    /**
     * Apply existing view data to query string
     *
     * @return void
     */
    public function updatedView(): void
    {
        if (isset($this->savedQueryData[$this->view])) {
            foreach ($this->savedQueryData[$this->view] as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }
}
