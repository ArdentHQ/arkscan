<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Facades\Network;
use App\Models\Transaction;
use App\Services\Blockchain\NetworkStatus;
use App\Services\NumberFormatter;
use App\ViewModels\Concerns\HasTimestamp;
use Spatie\ViewModels\ViewModel;

final class TransactionViewModel extends ViewModel
{
    use HasTimestamp;

    private Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->model = $transaction;
    }

    public function id(): string
    {
        return $this->model->id;
    }

    public function type(): string
    {
        return $this->model->type;
    }

    public function sender(): string
    {
        return $this->model->sender->address;
    }

    public function recipient(): string
    {
        return $this->model->recipient->address;
    }

    public function fee(): string
    {
        return NumberFormatter::currency($this->model->fee / 1e8, Network::currency());
    }

    public function amount(): string
    {
        return NumberFormatter::currency($this->model->amount / 1e8, Network::currency());
    }

    public function confirmations(): string
    {
        return NumberFormatter::number(NetworkStatus::height() - $this->model->block->height);
    }
}
