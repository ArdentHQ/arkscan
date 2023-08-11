<?php

declare(strict_types=1);

namespace App\Http\Livewire\Delegates;

use App\Facades\Network;
use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\HasTableFilter;
use App\Http\Livewire\Concerns\HasTablePagination;
use App\Models\Scopes\OrderByBalanceScope;
use App\Models\Wallet;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;

/**
 * @property bool $isAllSelected
 * @property LengthAwarePaginator $delegates
 * */
final class Delegates extends Component
{
    use DeferLoading;
    use HasTableFilter;
    use HasTablePagination;

    public const PER_PAGE = 51;

    public array $filter = [
        'active'   => true,
        'standby'  => true,
        'resigned' => true,
    ];

    /** @var mixed */
    protected $listeners = [
        'setDelegatesReady' => 'setIsReady',
    ];

    public function queryString(): array
    {
        return [
            'active'   => ['except' => true],
            'standby'  => ['except' => true],
            'resigned' => ['except' => true],
        ];
    }

    public function mount(bool $deferLoading = true): void
    {
        if (! $deferLoading) {
            $this->setIsReady();
        }
    }

    public function render(): View
    {
        return view('livewire.delegates.delegates', [
            'delegates'  => ViewModelFactory::paginate($this->delegates),
        ]);
    }

    // TODO: Filters - https://app.clickup.com/t/861n4ydmh - see WalletTransactionTable#getNoResultsMessageProperty
    public function getNoResultsMessageProperty(): null|string
    {
        if ($this->delegates->total() === 0) {
            return trans('tables.delegates.no_results.no_results');
        }

        if (! $this->hasFilters()) {
            return trans('tables.transactions.no_results.no_addressing_filters');
        }

        return null;
    }

    public function getDelegatesProperty(): LengthAwarePaginator
    {
        $emptyResults = new LengthAwarePaginator([], 0, $this->perPage);
        if (! $this->isReady) {
            return $emptyResults;
        }

        if (! $this->hasFilters()) {
            return $emptyResults;
        }

        return $this->getDelegatesQuery()
            ->withScope(OrderByBalanceScope::class)
            ->paginate($this->perPage);
    }

    public function getShowMissedBlocksProperty(): bool
    {
        if ($this->page > 1) {
            return false;
        }

        if ($this->filter['active'] === false) {
            return false;
        }

        return true;
    }

    public function perPageOptions(): array
    {
        return trans('tables.delegates.delegate_per_page_options');
    }

    private function hasFilters(): bool
    {
        if ($this->filter['active'] === true) {
            return true;
        }

        if ($this->filter['standby'] === true) {
            return true;
        }

        return $this->filter['resigned'] === true;
    }

    private function getDelegatesQuery(): Builder
    {
        return Wallet::query()
            ->whereNotNull('attributes->delegate->username')
            ->where(fn ($query) => $query->when($this->hasFilters(), function ($query) {
                $query->where(fn ($query) => $query->when($this->filter['active'] === true, fn ($query) => $query->where(function ($query) {
                            $query->where('attributes->delegate->resigned', null)
                                ->orWhere('attributes->delegate->resigned', false);
                        })->whereRaw('(attributes->\'delegate\'->>\'rank\')::int <= ?', Network::delegateCount())))
                    ->orWhere(fn ($query) => $query->when($this->filter['standby'] === true, fn ($query) => $query->where(function ($query) {
                            $query->where('attributes->delegate->resigned', null)
                                ->orWhere('attributes->delegate->resigned', false);
                        })->where(function ($query) {
                            $query->whereRaw('(attributes->\'delegate\'->>\'rank\')::int > ?', Network::delegateCount());
                        })))
                    ->orWhere(fn ($query) => $query->when($this->filter['resigned'] === true, fn ($query) => $query->where('attributes->delegate->resigned', true)));
            }))
            ->orderByRaw("(\"attributes\"->'delegate'->>'rank')::numeric ASC");
    }
}
