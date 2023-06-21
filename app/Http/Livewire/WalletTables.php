<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Wallets;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class WalletTables extends Component
{
    public string $address;

    public string $view = 'transactions';

    public ?string $previousView = null;

    public array $tabQueryData = [
        'transactions' => [
            'page'          => 1,
            'perPage'       => WalletTransactionTable::PER_PAGE,
            'outgoing'      => true,
            'incoming'      => true,
            'transfers'     => true,
            'votes'         => true,
            'multipayments' => true,
            'others'        => true,
        ],
    ];

    /** @var mixed */
    protected $listeners = [
        'showWalletView',
    ];

    public function __get($property): mixed
    {
        if (isset($this->tabQueryData[$this->view][$property])) {
            return $this->tabQueryData[$this->view][$property];
        }

        if (isset($this->tabQueryData[$this->previousView][$property])) {
            return $this->tabQueryData[$this->previousView][$property];
        }

        return parent::__get($property);
    }

    // public array $savedQueryData = [];

    public function queryString(): array
    {
        return [
            'view'          => ['except' => 'transactions'],

            // Transaction Filters
            'outgoing'      => ['except' => true],
            'incoming'      => ['except' => true],
            'transfers'     => ['except' => true],
            'votes'         => ['except' => true],
            'multipayments' => ['except' => true],
            'others'        => ['except' => true],
        ];
    }

    // public function __set($property, $value): void
    // {
    //     if (isset($this->tabQueryData[$this->view][$property])) {
    //         $this->tabQueryData[$this->view][$property] = $value;
    //     }
    // }

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

        $queryStringData = $this->queryString();

        // $this->savedQueryData[$this->view] = $this->tabQueryData[$this->view];
        foreach ($this->tabQueryData[$this->view] as $key => &$value) {
            if (! array_key_exists($key, $queryStringData)) {
                continue;
            }

            if (! array_key_exists('except', $queryStringData[$key])) {
                continue;
            }

            $value = $queryStringData[$key]['except'];
        }

        // if (isset($this->savedQueryData[$newView])) {
        //     $this->tabQueryData[$newView] = $this->savedQueryData[$newView];
        // }
    }
}
