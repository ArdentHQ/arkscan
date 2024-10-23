<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Wallets;
use App\Http\Livewire\Abstracts\TabbedTableComponent;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

/** @property LengthAwarePaginator $blocks */
final class WalletBlockTable extends TabbedTableComponent
{
    public string $address;

    /** @var mixed */
    protected $listeners = [
        'setBlocksReady'  => 'setIsReady',
        'currencyChanged' => '$refresh',
        'reloadBlocks'    => '$refresh',
    ];

    public function mount(WalletViewModel $wallet): void
    {
        $this->address = $wallet->address();
    }

    public function render(): View
    {
        return view('livewire.wallet-block-table', [
            'wallet' => ViewModelFactory::make(Wallets::findByAddress($this->address)),
            'blocks' => ViewModelFactory::paginate($this->blocks),
        ]);
    }

    public function getNoResultsMessageProperty(): ?string
    {
        if ($this->blocks->total() === 0) {
            return trans('tables.wallet.blocks.no_results');
        }

        return null;
    }

    public function getBlocksProperty(): LengthAwarePaginator
    {
        if (! $this->isReady) {
            return new LengthAwarePaginator([], 0, $this->perPage);
        }

        return Block::where('generator_address', $this->address)
            ->withScope(OrderByHeightScope::class)
            ->paginate($this->perPage);
    }
}
