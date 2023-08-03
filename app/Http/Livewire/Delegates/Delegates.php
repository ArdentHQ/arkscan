<?php

declare(strict_types=1);

namespace App\Http\Livewire\Delegates;

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

    // TODO: Filters - https://app.clickup.com/t/861n4ydmh - see WalletTransactionTable
    public array $filter = [];

    /** @var mixed */
    protected $listeners = [
        'setDelegatesReady' => 'setIsReady',
    ];

    public function queryString(): array
    {
        // TODO: Filters - https://app.clickup.com/t/861n4ydmh - see WalletTransactionTable
        return [];
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

        return null;
    }

    public function getDelegatesProperty(): LengthAwarePaginator
    {
        $emptyResults = new LengthAwarePaginator([], 0, $this->perPage);
        if (! $this->isReady) {
            return $emptyResults;
        }

        // TODO: Filters - https://app.clickup.com/t/861n4ydmh - see WalletTransactionTable#getTransactionsProperty

        return $this->getDelegatesQuery()
            ->withScope(OrderByBalanceScope::class)
            ->paginate($this->perPage);
    }

    // TODO: return false if "active" filter is not selected - https://app.clickup.com/t/861n4ydmh
    public function getShowMissedBlocksProperty(): bool
    {
        if ($this->page > 1) {
            return false;
        }

        return true;
    }

    public function perPageOptions(): array
    {
        return trans('tables.delegates.delegate_per_page_options');
    }

    // TODO: Filters - https://app.clickup.com/t/861n4ydmh - see WalletTransactionTable
    private function getDelegatesQuery(): Builder
    {
        return Wallet::query()
            ->whereNotNull('attributes->delegate->username')
            ->orderByRaw("(\"attributes\"->'delegate'->>'rank')::numeric ASC");
    }
}
