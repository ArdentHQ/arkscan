<?php

declare(strict_types=1);

namespace App\Http\Livewire\Wallet\Concerns;

use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;

/** @property LengthAwarePaginator $blocks */
trait BlocksTab
{
    public $blocksIsReady = false;

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

    public function getBlocksNoResultsMessageProperty(): ?string
    {
        if ($this->blocks->total() === 0) {
            return trans('tables.wallet.blocks.no_results');
        }

        return null;
    }

    public function getBlocksProperty(): LengthAwarePaginator
    {
        if (! $this->isReady) {
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
