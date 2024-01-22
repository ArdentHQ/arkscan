<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Wallets;
use App\Http\Livewire\Abstracts\TabbedTableComponent;
use App\Http\Livewire\Concerns\DeferLoading;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

/** @property LengthAwarePaginator $blocks */
final class WalletBlockTable extends TabbedTableComponent
{
    use DeferLoading;

    public string $publicKey;

    /** @var mixed */
    protected $listeners = [
        'setBlocksReady'              => 'setIsReady',
        'currencyChanged'             => '$refresh',
        'echo:wallet-blocks,NewBlock' => '$refresh',
    ];

    public function mount(WalletViewModel $wallet): void
    {
        /** @var string $publicKey */
        $publicKey = $wallet->publicKey();

        $this->publicKey = $publicKey;
    }

    public function render(): View
    {
        return view('livewire.wallet-block-table', [
            'wallet' => ViewModelFactory::make(Wallets::findByPublicKey($this->publicKey)),
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

        return Block::where('generator_public_key', $this->publicKey)
            ->withScope(OrderByHeightScope::class)
            ->paginate($this->perPage);
    }
}
