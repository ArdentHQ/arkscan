<?php

declare(strict_types=1);

namespace App\Http\Livewire\Delegates;

use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\HasTableFilter;
use App\Http\Livewire\Concerns\HasTablePagination;
use App\Models\Scopes\OrderByBalanceScope;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Timestamp;
use App\ViewModels\ViewModelFactory;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;

/**
 * @property bool $isAllSelected
 * @property LengthAwarePaginator $delegates
 * */
final class RecentVotes extends Component
{
    use DeferLoading;
    use HasTableFilter;
    use HasTablePagination;

    // TODO: Filters - https://app.clickup.com/t/861n4ydmh - see WalletTransactionTable
    public array $filter = [];

    /** @var mixed */
    protected $listeners = [
        'setRecentVotesReady' => 'setIsReady',
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
        return view('livewire.delegates.recent-votes', [
            'votes' => ViewModelFactory::paginate($this->recentVotes),
        ]);
    }

    // TODO: Filters - https://app.clickup.com/t/861n4ydmh - see WalletTransactionTable#getNoResultsMessageProperty
    public function getNoResultsMessageProperty(): null|string
    {
        if ($this->recentVotes->total() === 0) {
            return trans('tables.recent-votes.no_results.no_results');
        }

        return null;
    }

    public function getRecentVotesProperty(): LengthAwarePaginator
    {
        $emptyResults = new LengthAwarePaginator([], 0, $this->perPage);
        if (! $this->isReady) {
            return $emptyResults;
        }

        // TODO: Filters - https://app.clickup.com/t/861n4ydmh - see WalletTransactionTable#getTransactionsProperty

        return $this->getRecentVotesQuery()
            ->paginate($this->perPage);
    }

    // TODO: Filters - https://app.clickup.com/t/861n4ydmh - see WalletTransactionTable
    private function getRecentVotesQuery(): Builder
    {
        return Transaction::query()
            ->withScope(OrderByTimestampScope::class)
            ->where('type', 3)
            ->where('timestamp', '>=', Timestamp::now()->sub(30, 'days')->unix());
    }
}
