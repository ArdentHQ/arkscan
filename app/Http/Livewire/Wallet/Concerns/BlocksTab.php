<?php

declare(strict_types=1);

namespace App\Http\Livewire\Wallet\Concerns;

use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\ViewModels\WalletViewModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;

/** @property LengthAwarePaginator $blocks */
trait BlocksTab
{
    public bool $blocksIsReady = false;

    public function getListenersBlocksTab(): array
    {
        return [
            'reloadBlocks' => '$refresh',
        ];
    }

    public function queryStringBlocksTab(): array
    {
        return [
            'paginators.blocks'        => ['except' => 1, 'as' => 'page', 'history' => true],
            'paginatorsPerPage.blocks' => ['except' => self::defaultPerPage('BLOCKS'), 'as' => 'per-page', 'history' => true],
        ];
    }

    // We're keeping it here as TabbedComponent has its own mount method
    // and we can't override it with arguments. Wallet is declared here as it's used
    // in the mount method of TransactionsTab.
    // @phpstan-ignore-next-line - ignoring the type as we are not using it in this trait.
    public function mountBlocksTab(WalletViewModel $wallet, bool $deferLoading = true): void
    {
        if (! $deferLoading) {
            $this->setBlocksReady();
        }
    }

    public function getBlocksNoResultsMessageProperty(): ?string
    {
        if ($this->blocks->total() === 0) {
            return trans('tables.wallet.blocks.no_results');
        }

        return null;
    }

    public function getBlocksProperty(): LengthAwarePaginator
    {
        if (! $this->blocksIsReady) {
            return new LengthAwarePaginator([], 0, $this->getPerPage('blocks'), $this->getPage('blocks'));
        }

        return Block::where('proposer', $this->address)
            ->withScope(OrderByHeightScope::class)
            ->paginate($this->getPerPage('blocks'), page: $this->getPage('blocks'));
    }

    #[On('setBlocksReady')]
    public function setBlocksReady(): void
    {
        $this->blocksIsReady = true;
    }
}
