<?php

declare(strict_types=1);

namespace App\Http\Livewire\Wallet\Concerns;

use App\Models\Scopes\OrderByBalanceScope;
use App\Models\Wallet;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;

/** @property LengthAwarePaginator $wallets */
trait VotersTab
{
    public $votersIsReady = false;

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

    public function getVotersNoResultsMessageProperty(): ?string
    {
        if ($this->voters->total() === 0) {
            return trans('tables.wallets.no_results');
        }

        return null;
    }

    public function getVotersProperty(): LengthAwarePaginator
    {
        if (! $this->isReady) {
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
