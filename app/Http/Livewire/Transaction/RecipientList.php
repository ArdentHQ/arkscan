<?php

declare(strict_types=1);

namespace App\Http\Livewire\Transaction;

use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\HasTablePagination;
use App\Models\Transaction;
use App\ViewModels\TransactionViewModel;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Component;

/**
 * @property LengthAwarePaginator $recipients
 * */
final class RecipientList extends Component
{
    use DeferLoading;
    use HasTablePagination;

    public const PER_PAGE = 10;

    public string $transactionId;

    /** @var mixed */
    protected $listeners = [
        'currencyChanged' => '$refresh',
    ];

    public function mount(string $transactionId): void
    {
        $this->transactionId = $transactionId;
    }

    public function render(): View
    {
        return view('livewire.transaction.recipient-list', [
            'recipients' => $this->recipients,
        ]);
    }

    public function getRecipientsProperty(): LengthAwarePaginator
    {
        if (! $this->isReady) {
            return new LengthAwarePaginator([], 0, $this->perPage);
        }

        $recipients = new Collection(
            (new TransactionViewModel(Transaction::find($this->transactionId)))
                ->payments(true)
        );

        $totalCount = $recipients->count();

        $items = $recipients->chunk($this->perPage)
            ->get($this->page - 1);

        return new LengthAwarePaginator($items, $totalCount, $this->perPage, $this->page, [
            'pageName' => 'page',
        ]);
    }
}
