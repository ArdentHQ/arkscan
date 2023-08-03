<?php

declare(strict_types=1);

namespace App\Http\Livewire\Delegates;

use App\Enums\CoreTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Facades\Network;
use App\Facades\Wallets;
use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\HasTablePagination;
use App\Models\Scopes\OrderByBalanceScope;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\Models\Wallet;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use ArkEcosystem\Crypto\Enums\Types;
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
    use HasTablePagination;

    // TODO: Filters - https://app.clickup.com/t/861n4ydmh - see WalletTransactionTable
    public array $filter = [];

    public bool $selectAllFilters = true;

    /** @var mixed */
    protected $listeners = [
        'setDelegatesReady' => 'setIsReady',
    ];

    public function __get(mixed $property): mixed
    {
        if (array_key_exists($property, $this->filter)) {
            return $this->filter[$property];
        }

        return parent::__get($property);
    }

    public function __set(string $property, mixed $value): void
    {
        if (array_key_exists($property, $this->filter)) {
            $this->filter[$property] = $value;
        }
    }

    public function queryString(): array
    {
        // TODO: Filters - https://app.clickup.com/t/861n4ydmh - see WalletTransactionTable
        return [];
    }

    public function mount(bool $deferLoading = true): void
    {
        foreach ($this->filter as &$filter) {
            if (in_array($filter, ['1', 'true', true], true)) {
                $filter = true;
            } elseif (in_array($filter, ['0', 'false', false], true)) {
                $filter = false;
            }
        }

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

    public function getIsAllSelectedProperty(): bool
    {
        return ! collect($this->filter)->contains(false);
    }

    // TODO: Filters - https://app.clickup.com/t/861n4ydmh - see WalletTransactionTable#getNoResultsMessageProperty
    public function getNoResultsMessageProperty(): null|string
    {
        if ($this->delegates->total() === 0) {
            return trans('tables.delegates.no_results.no_results');
        }

        return null;
    }

    public function updatedSelectAllFilters(bool $value): void
    {
        foreach ($this->filter as &$filter) {
            $filter = $value;
        }
    }

    public function updatedFilter(): void
    {
        $this->selectAllFilters = $this->isAllSelected;

        $this->setPage(1);
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

    // TODO: don't show if "active" filter is not selected - https://app.clickup.com/t/861n4ydmh
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
    private function hasAddressingFilters(): bool
    {
        return false;
    }

    // TODO: Filters - https://app.clickup.com/t/861n4ydmh - see WalletTransactionTable
    private function hasTransactionTypeFilters(): bool
    {
        return false;
    }

    // TODO: Filters - https://app.clickup.com/t/861n4ydmh - see WalletTransactionTable
    private function getDelegatesQuery(): Builder
    {
        return Wallet::query()
            ->whereNotNull('attributes->delegate->username')
            ->orderByRaw("(\"attributes\"->'delegate'->>'rank')::numeric ASC");
        // return Transaction::query()
        //     ->where(function ($query) {
        //         $query->where(fn ($query) => $query->when($this->filter['transfers'] === true, fn ($query) => $query->where('type', CoreTransactionTypeEnum::TRANSFER)))
        //             ->orWhere(fn ($query) => $query->when($this->filter['votes'] === true, fn ($query) => $query->where('type', CoreTransactionTypeEnum::VOTE)))
        //             ->orWhere(fn ($query) => $query->when($this->filter['multipayments'] === true, fn ($query) => $query->where('type', CoreTransactionTypeEnum::MULTI_PAYMENT)))
        //             ->orWhere(fn ($query) => $query->when($this->filter['others'] === true, fn ($query) => $query
        //                 ->where('type_group', TransactionTypeGroupEnum::MAGISTRATE)
        //                 ->orWhere(
        //                     fn ($query) => $query
        //                         ->where('type_group', TransactionTypeGroupEnum::CORE)
        //                         ->whereNotIn('type', [
        //                             CoreTransactionTypeEnum::TRANSFER,
        //                             CoreTransactionTypeEnum::VOTE,
        //                             CoreTransactionTypeEnum::MULTI_PAYMENT,
        //                         ])
        //                 )));
        //     })
        //     ->where(function ($query) {
        //         $query->where(fn ($query) => $query->when($this->filter['outgoing'], fn ($query) => $query->where('sender_public_key', $this->publicKey)))
        //             ->orWhere(fn ($query) => $query->when($this->filter['incoming'], fn ($query) => $query->where('recipient_id', $this->address)))
        //             ->orWhere(fn ($query) => $query->when($this->filter['incoming'], fn ($query) => $query
        //                 ->where('type', Types::MULTI_PAYMENT)
        //                 ->whereJsonContains('asset->payments', [['recipientId' => $this->address]])));
        //     });
    }
}
