<?php

declare(strict_types=1);

namespace App\Http\Livewire\Wallet\Concerns;

use App\Models\Scopes\OrderByBalanceScope;
use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;

/** @property LengthAwarePaginator $voters */
trait VotersTab
{
    public bool $votersIsReady = false;

    public function getListenersVotersTab(): array
    {
        return [
            'reloadVoters' => '$refresh',
        ];
    }

    public function queryStringVotersTab(): array
    {
        return [
            'paginators.voters'        => ['except' => 1, 'as' => 'page', 'history' => true],
            'paginatorsPerPage.voters' => ['except' => self::defaultPerPage('VOTERS'), 'as' => 'per-page', 'history' => true],
        ];
    }

    // We're keeping it here as TabbedComponent has its own mount method
    // and we can't override it with arguments. Wallet is declared here as it's used
    // in the mount method of TransactionsTab.
    // @phpstan-ignore-next-line - ignoring the type as we are not using it in this trait.
    public function mountVotersTab(WalletViewModel $wallet, bool $deferLoading = true): void
    {
        if (! $deferLoading) {
            $this->setVotersReady();
        }
    }

    public function getVotersNoResultsMessageProperty(): ?string
    {
        if ($this->voters->total() === 0) {
            return trans('tables.wallets.no_results');
        }

        return null;
    }

    public function getVotersProperty(): LengthAwarePaginator
    {
        if (! $this->votersIsReady) {
            return new LengthAwarePaginator([], 0, $this->getPerPage('voters'), $this->getPage('voters'));
        }

        return Wallet::where('attributes->vote', $this->address)
            ->withScope(OrderByBalanceScope::class)
            ->paginate($this->getPerPage('voters'), page: $this->getPage('voters'));
    }

    #[On('setVotersReady')]
    public function setVotersReady(): void
    {
        $this->votersIsReady = true;
    }
}
