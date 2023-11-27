<?php

declare(strict_types=1);

namespace App\Http\Livewire\Transactions;

use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\HasTablePagination;
use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @property LengthAwarePaginator $recipients
 * */
final class Recipients
{
    use DeferLoading;
    use HasTablePagination;

    public const PER_PAGE = 64;

    public Transaction $transaction;

    /** @var mixed */
    protected $listeners = [
        'currencyChanged' => '$refresh',
    ];

    public function mount(Transaction $transaction): void
    {
        $this->transaction = $transaction;
    }

    public function render() //: View
    {
        return '';
        // return view('livewire.transaction.recipient-list', [
        //     'recipients' => ViewModelFactory::paginate($this->recipients),
        // ]);
    }

    // public function getRecipientsProperty(): LengthAwarePaginator
    // {
    //     $recipients = [];
    //     if ($this->isReady) {
    //         $recipients = $this->transaction->recipients();
    //     }

    //     return new LengthAwarePaginator($recipients, 0, $this->perPage);
    // }
}
